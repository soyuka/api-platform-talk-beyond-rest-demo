<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Bookmark;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\DBAL\Event\SchemaCreateTableEventArgs;
use Doctrine\ORM\Events;
use Elasticsearch\Client as Elasticsearch;

class BookmarkSubscriber implements EventSubscriber
{
    private $elasticsearch;
    private $screenshotDirectory;

    public function __construct(Elasticsearch $elasticsearch, $screenshotDirectory)
    {
        $this->elasticsearch = $elasticsearch;
        $this->screenshotDirectory = $screenshotDirectory;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if (!$entity instanceof Bookmark) {
            return;
        }

        $path = sprintf('%s/%s', $this->screenshotDirectory, $entity->screenshot);

        if (file_exists($path)) {
            unlink($path);
        }

        $this->elasticsearch->delete([
            'id' => $entity->getId(),
            'index' => Bookmark::ELASTICSEARCH_INDEX,
            'type' => Bookmark::ELASTICSEARCH_TYPE,
        ]);
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->index($event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->index($event);
    }

    private function index(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if (!$entity instanceof Bookmark) {
            return;
        }

        $res = $this->elasticsearch->index([
            'index' => Bookmark::ELASTICSEARCH_INDEX,
            'type' => Bookmark::ELASTICSEARCH_TYPE,
            'id' => (string) $entity->getId(),
            'body' => [
                'date' => $entity->getCreated()->format('Y-m-d H:i:s'),
                'title' => $entity->title,
                'description' => $entity->description,
                'slug' => $entity->slug,
                'link' => $entity->link,
                'tags' => [],
            ],
        ]);

        $this->elasticsearch->indices()->refresh();
    }

    public function onSchemaCreateTable(SchemaCreateTableEventArgs $event)
    {
        $indices = $this->elasticsearch->indices();

        if ($indices->exists(['index' => Bookmark::ELASTICSEARCH_INDEX])) {
            $indices->delete(['index' => Bookmark::ELASTICSEARCH_INDEX]);
        }

        $indices->create([
            'index' => Bookmark::ELASTICSEARCH_INDEX,
            'body' => [
                'mappings' => [
                    Bookmark::ELASTICSEARCH_TYPE => [
                        'properties' => [
                            'id' => ['type' => 'keyword'],
                            'date' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
                            'title' => ['type' => 'text'],
                            'description' => ['type' => 'text'],
                            'keywords' => ['type' => 'text'],
                            'slug' => ['type' => 'text'],
                            'link' => ['type' => 'text'],
                            'tags' => ['type' => 'keyword'],
                        ],
                        'dynamic' => 'strict',
                    ],
                ],
            ],
        ]);
    }
}
