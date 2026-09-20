<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

interface PageInterface {
    public function services(): array;
    public function context(): array;
 }