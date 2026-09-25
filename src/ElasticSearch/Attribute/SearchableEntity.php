<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SearchableEntity
{
    public function __construct(
        public readonly string $indexName,
    ) {}
}