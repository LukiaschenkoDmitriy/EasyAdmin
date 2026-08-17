<?php declare(strict_types=1);

namespace EAdmin\Core\Vite;

final class ViteManifest {
    private array $data;

    public function __construct(private readonly string $manifestPath)
    {
        $this->data = is_file($manifestPath) ? json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR) : [];
    }

    public function entry(string $key): ?ViteManifestEntry
    {
        if (!isset($this->data[$key])) return null;

        $e = $this->data[$key];

        return new ViteManifestEntry(
            file: '/build/' . $e['file'],
            css: array_map(fn($c) => '/build/' . $c, $e['css'] ?? []),
        );
    }
}