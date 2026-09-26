<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

class RepositoryResult {
    public function __construct(
        public array $entities, 
        public RepositoryPagination $pagination
    ) { }
}