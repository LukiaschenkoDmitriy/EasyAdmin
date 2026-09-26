<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

interface RepositoryInterface {
    public function get(string $entityClass, RepositoryContext $context, array $sortingFields, array $searchFields): mixed;
}