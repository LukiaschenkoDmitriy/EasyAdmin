<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch;

use Elastica\Bulk;
use Elastica\Bulk\ResponseSet;
use Elastica\Document;
use Elastica\Mapping;
use FOS\ElasticaBundle\Elastica\Client;

class IndexManager
{
    public function __construct(
        private Client $client,
        private MappingBuilder $mappingBuilder,
        private DocumentExtractor $extractor
    ) {}

    public function getClient(): Client
    {
        return $this->client;
    }

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

    public function bulkIndex(string $indexName, array $entities, callable $getId): int
    {
        if (empty($entities)) {
            return 0;
        }

        $index = $this->client->getIndex($indexName);
        $bulk = new Bulk($this->client);
        $bulk->setIndex($index);

        foreach ($entities as $entity) {
            $data = $this->extractor->extract($entity);
            $id = $getId($entity);

            $bulk->addDocument(new Document((string) $id, $data));
        }

        $response = $bulk->send();

        if ($response->hasError()) {
            throw new \RuntimeException('Bulk indexing failed: ' . $this->collectBulkErrors($response));
        }

        $index->refresh();

        return count($entities);
    }

    private function collectBulkErrors(ResponseSet $response): string
    {
        $errors = [];
        foreach ($response->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                $errors[] = $bulkResponse->getErrorMessage();
            }
        }

        return implode('; ', $errors);
    }

}