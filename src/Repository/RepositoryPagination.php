<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

class RepositoryPagination {
    public function __construct(
        public int $total, 
        public int $page, 
        public int $pages,
        public bool $next
    ) { }
}