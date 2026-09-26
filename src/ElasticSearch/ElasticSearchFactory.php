<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch;

use FOS\ElasticaBundle\Elastica\Client;
use FOS\ElasticaBundle\Elastica\Index;

class ElasticSearchFactory {
    public function __construct(private Client $client) {  }
    public function create(string $indexName): ?Index {
        $index = $this->client->getIndex($indexName);

        if (!$index->exists()) {
            return null;
        }

        return $index;
    }
}