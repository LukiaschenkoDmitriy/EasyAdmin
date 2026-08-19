<?php declare(strict_types=1);

use EAdmin\Core\Assets\AssetMapperResolver;
use EAdmin\Core\Assets\AssetResolverInterface;
use EAdmin\Core\Assets\ViteAssetResolver;
use EAdmin\Core\Command\InitCommand;
use EAdmin\Core\Component\Component;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Twig\Extensions\AttributeExtension;
use EAdmin\Core\Twig\Extensions\ScriptExtension;
use EAdmin\Core\Twig\Extensions\SlotExtension;
use EAdmin\Core\Twig\Extensions\StyleExtension;
use EAdmin\Core\Vite\ViteManifest;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return function (ContainerConfigurator $container): void {
    $services = $container->services()->defaults()->autoconfigure()->autowire();

    $services->set(Component::class);

    $services->set(AssetMapperResolver::class);
    $services->set(ViteAssetResolver::class)->arg('$viteConfig', param("eadmin.assets.vite"));
    $services->set(ViteManifest::class)->arg('$viteConfig', param("eadmin.assets.vite"));

    $services->set(InitCommand::class)->args([param("kernel.project_dir")])->tag('make.command');

    $services->set(ScriptExtension::class)->tag("twig.extension");
    $services->set(StyleExtension::class)->tag("twig.extension");
    $services->set(SlotExtension::class)->tag("twig.extension");
    $services->set(AttributeExtension::class)->tag("twig.extension");

    $services->set(ComponentRenderer::class);
};