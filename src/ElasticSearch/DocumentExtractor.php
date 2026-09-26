<?php declare(strict_types=1);

namespace EAdmin\Core\ElasticSearch;

use EAdmin\Core\ElasticSearch\Attribute\SearchField;

class DocumentExtractor {
    public function extract(object $entity): array
    {
        $reflection = new \ReflectionClass($entity);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            $attrs = $property->getAttributes(SearchField::class);
            if (empty($attrs)) {
                continue;
            }

            $property->setAccessible(true);
            $value = $property->getValue($entity);

            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(\DateTimeInterface::ATOM);
            }

            $data[$property->getName()] = $value;
        }

        return $data;
    }

}