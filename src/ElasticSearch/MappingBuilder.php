<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch;

use EAdmin\Core\ElasticSearch\Attribute\SearchableEntity;
use EAdmin\Core\ElasticSearch\Attribute\SearchField;

class MappingBuilder
{
    /**
     * Читає Entity через reflection і будує ES-мапінг + список полів для індексації
     */
    public function buildFromEntity(string $entityClass): array
    {
        $reflection = new \ReflectionClass($entityClass);

        $classAttr = $reflection->getAttributes(SearchableEntity::class)[0] ?? null;
        if (!$classAttr) {
            throw new \Exception("Not found SearchableEntity attribute for class: " . $entityClass);
        }

        /** @var SearchableEntity $searchable */
        $searchable = $classAttr->newInstance();

        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            $fieldAttrs = $property->getAttributes(SearchField::class);
            if (empty($fieldAttrs)) {
                continue;
            }

            /** @var SearchField $field */
            $field = $fieldAttrs[0]->newInstance();
            $name = $property->getName();

            $mapping = ['type' => $field->type];
            if ($field->analyzer) {
                $mapping['analyzer'] = $field->analyzer;
            }
            if ($field->boost !== 1.0) {
                $mapping['boost'] = $field->boost;
            }

            $properties[$name] = $mapping;
        }

        return [
            'indexName' => $searchable->indexName,
            'properties' => $properties,
        ];
    }
}