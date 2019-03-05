<?php

namespace App\MessageHandler;

use App\EventListener\CreateElasticsearchIndex;
use Doctrine\Common\Persistence\ManagerRegistry;
use Elasticsearch\Endpoints\Create;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Entity\Bookmark;
use Spatie\Browsershot\Browsershot;
use DOMDocument;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Elasticsearch\Client as Elasticsearch;
use ApiPlatform\Core\Bridge\Elasticsearch\Metadata\Document\DocumentMetadata;

function log($message) {
    echo $message . PHP_EOL;
}

final class BookmarkHandler implements MessageHandlerInterface
{
    private $elasticsearch;
    private $registry;
    private $screenshotDirectory;

    public function __construct(ManagerRegistry $registry, Elasticsearch $elasticsearch, $screenshotDirectory)
    {
        $this->elasticsearch = $elasticsearch;
        $this->registry = $registry;
        $this->screenshotDirectory = $screenshotDirectory;
    }

    public function __invoke(Bookmark $message)
    {
        $manager = $this->registry->getManagerForClass(Bookmark::class);
        log("Processing {$message->link}");

        // Persist with the link only, the rest can take time
        $manager->persist($message);
        $manager->flush();

        try {
            $url = Browsershot::url($message->link)->dismissDialogs()->noSandbox();
            $this->readMeta($url, $message);
            log("Read metadata for {$message->link}");
            $manager->persist($message);
            $manager->flush();
        } catch (ProcessFailedException $e) {
            log("Error while reading the link {$message->link}");
        }

        // Screenshot
        $message->screenshot = $message->slug . '.png';

        try {
            $url->windowSize(1920, 1080)
                ->save(sprintf('%s/%s', $this->screenshotDirectory, $message->screenshot));
                log("Took a screenshot for {$message->link}");
            $manager->persist($message);
            $manager->flush();
        } catch (ProcessFailedException $e) {
            log("Error while reading the link {$message->link}");
        }

        $this->persistElasticsearch($message);
    }

    private function readMeta($url, Bookmark &$message) {
        $dom = new DOMDocument();
        @$dom->loadHTML($url->bodyHtml());

        $title = $dom->getElementsByTagName('title')[0] ?? null;
        $message->title = null === $title ? $message->slug : trim($title->childNodes[0]->data);

        foreach ($dom->getElementsByTagName('meta') as $node) {
            if (null !== $name = $node->attributes->getNamedItem('name')) {
                switch ($name->value) {
                    case 'keywords':
                        $message->keywords = explode(', ', $node->attributes->getNamedItem('content')->value);
                        break;
                    case 'description':
                        $message->description = $node->attributes->getNamedItem('content')->value;
                        break;
                }

                continue;
            }

            if (null === $property = $node->attributes->getNamedItem('property')) {
                continue;
            }

            switch ($property->value) {
                case 'og:image':
                case 'twitter:image':
                    $message->image = $node->attributes->getNamedItem('content')->value;
                    break;
            }
         }
    }

    private function persistElasticsearch(Bookmark $message)
    {
        $this->elasticsearch->index([
            'index' => CreateElasticsearchIndex::INDEX,
            'type' => 'bookmark',
            'id' => (string) $message->getId(),
            'body' => [
                'date' => $message->getCreated()->format('Y-m-d H:i:s'),
                'title' => $message->title,
                'description' => $message->description,
                'keywords' => $message->keywords,
                'tags' => []
            ]
        ]);

        $this->elasticsearch->indices()->refresh();
    }
}
