<?php declare(strict_types=1);

namespace EAdmin\Core\Assets;

use EAdmin\Core\Vite\ViteManifest;

final class ViteAssetResolver implements AssetResolverInterface
{
    public function __construct(
        private readonly ViteManifest $manifest,
        private readonly array $viteConfig,
    ) {}

    public function resolve(string $templatePath): ResolvedAssets
    {
        $base = preg_replace('/\.html\.twig$/', '', $templatePath);
        $base = preg_replace('/\.ts$/', '', $base);
        $base = preg_replace('/\.js$/', '', $base);
        $base = preg_replace('/\.css$/', '', $base);
        $base = preg_replace('/\.scss$/', '', $base);

        $scripts = [];
        $styles = [];

        if ($this->viteConfig['typescript']) {
            $ts = $this->manifest->entry('src/EAdmin/' . $base . '.ts');
            if ($ts !== null) {
                $scripts[] = $ts->file;
                array_push($styles, ...$ts->css);
            }
        }

        if ($this->viteConfig['scss']) {
            $scss = $this->manifest->entry('src/EAdmin/' . $base . '.scss');
            if ($scss !== null) {
                $styles[] = $scss->file;
            }
        }

        return new ResolvedAssets($scripts, $styles);
    }
}