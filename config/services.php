<?php declare(strict_types=1);

use EAdmin\Core\Assets\AssetMapperResolver;
use EAdmin\Core\Assets\ViteAssetResolver;
use EAdmin\Core\Command\InitCommand;
use EAdmin\Core\Component\Component;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Twig\Extensions\ScriptExtension;
use EAdmin\Core\Twig\Extensions\SlotExtension;
use EAdmin\Core\Twig\Extensions\StyleExtension;
use EAdmin\Core\Vite\ViteManifest;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return function (ContainerConfigurator $container): void {
    $container->services()->defaults()->autoconfigure()->autowire();

    $container->services()->set(Component::class);
    $container->services()->set(AssetMapperResolver::class);

    $container->services()
        ->set(ViteAssetResolver::class)
        ->arg('$viteConfig', param("eadmin.assets.vite"));

    $container->services()
        ->set(ViteManifest::class)
        ->arg('$manifestPath', param("eadmin.assets.vite"));

    $container->services()
        ->set(InitCommand::class)
        ->args([param("kernel.project_dir")])
        ->tag('console.command');

    $container->services()
        ->set(ScriptExtension::class)
        ->tag("twig.extension");

    $container->services()
        ->set(StyleExtension::class)
        ->tag("twig.extension");

    $container->services()
        ->set(SlotExtension::class)
        ->tag("twig.extension");

    $container->services()->set(ComponentRenderer::class);
};