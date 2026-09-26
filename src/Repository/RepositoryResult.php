<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

class RepositoryResult {
    public function __construct(
        public array $entities, 
        public int $total, 
        public int $page, 
        public int $pages,
        public bool $next,
    ) { }
}