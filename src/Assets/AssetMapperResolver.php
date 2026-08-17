<?php declare(strict_types=1);

namespace EAdmin\Core\Assets;

use Symfony\Component\AssetMapper\AssetMapperInterface;

final class AssetMapperResolver implements AssetResolverInterface
{
    public function __construct(private readonly AssetMapperInterface $assetMapper) {}

    public function resolve(string $templatePath): ResolvedAssets
    {
        $base = 'eadmin/' . preg_replace('/\.html\.twig$/', '', $templatePath);

        $scripts = [];
        $styles = [];

        if ($jsAsset = $this->assetMapper->getAsset($base . '.js')) {
            $scripts[] = $jsAsset->publicPath;
        }
        if ($cssAsset = $this->assetMapper->getAsset($base . '.css')) {
            $styles[] = $cssAsset->publicPath;
        }
        if ($scssAsset = $this->assetMapper->getAsset($base . '.scss')) {
            $styles[] = $scssAsset->publicPath;
        }

        return new ResolvedAssets($scripts, $styles);
    }
}