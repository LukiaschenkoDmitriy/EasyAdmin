<?php declare(strict_types=1);

namespace EAdmin\Core\Assets;

final readonly class ResolvedAssets
{
    /**
     * @param string[] $scripts
     * @param string[] $styles
     */
    public function __construct(
        public array $scripts = [],
        public array $styles = [],
    ) {}
}