<?php declare(strict_types=1);

namespace EAdmin\Core\Vite;

final class ViteManifestEntry {
    public function __construct(
        public string $file,
        public array $css = []
    ) {}
}