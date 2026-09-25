<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class SearchField
{
    public function __construct(
        public readonly string $type = 'text',
        public readonly ?string $analyzer = null,
        public readonly float $boost = 1.0,
        public readonly bool $filterable = false,
    ) {}
}