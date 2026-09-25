<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch;

use Elastica\Mapping;
use FOS\ElasticaBundle\Elastica\Client;

class IndexManager
{
    public function __construct(
        private Client $client,
        private MappingBuilder $mappingBuilder,
    ) {}

    public function createOrUpdateIndex(string $entityClass): void
    {
        $config = $this->mappingBuilder->buildFromEntity($entityClass);
        $index = $this->client->getIndex($config['indexName']);

        if (!$index->exists()) {
            $index->create([
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
            ]);
        }

        $mapping = new Mapping();
        $mapping->setProperties($config['properties']);
        $mapping->send($index);
    }
}