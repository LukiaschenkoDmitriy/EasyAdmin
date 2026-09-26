<?php declare(strict_types=1);

namespace EAdmin\Core\Repository;

class RepositoryContext {
    public function __construct(
        public ?string $sortingBy = null, 
        public ?string $order = null,
        public ?string $search = null,
        public ?string $searchBy = null,
        public int $limit = 25,
        public int $page = 1,
        public int $count = 0,
        public array $limits = [10,25,50,75,100]
    ) {
        if ($this->page < 1) $this->page = 1;
        if ($this->limit < min($this->limits)) $this->limit = min($this->limits);
        if ($this->limit > max($this->limits)) $this->limit = max($this->limits);
     }
}