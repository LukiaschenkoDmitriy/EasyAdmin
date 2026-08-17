<?php declare(strict_types=1);

namespace EAdmin\Core\Assets;

interface AssetResolverInterface
{
    public function resolve(string $templatePath): ResolvedAssets;
}