<?php

namespace App\EventListener;

use Doctrine\DBAL\Event\SchemaCreateTableEventArgs;
use Elasticsearch\Client as Elasticsearch;

class CreateElasticsearchIndex
{
    const INDEX = 'bookmarks';
    private $elasticsearch;

    public function __construct(Elasticsearch $elasticsearch) {
        $this->elasticsearch = $elasticsearch;
    }

    public function onSchemaCreateTable(SchemaCreateTableEventArgs $event)
    {
        $indices = $this->elasticsearch->indices();

        if ($indices->exists(['index' => self::INDEX])) {
            $indices->delete(['index' => self::INDEX]);
        }

        $indices->create([
            'index' => self::INDEX,
            'body' => [
                'mappings' => [
                    'bookmark' => [
                        'properties' => [
                            'id' => ['type' => 'keyword'],
                            'date' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
                            'title' => ['type' => 'text'],
                            'description' => ['type' => 'text'],
                            'keywords' => ['type' => 'text'],
                            'tags' => ['type' => 'keyword']
                        ],
                        'dynamic' => 'strict'
                    ]
                ]
            ],
        ]);

    }
}
