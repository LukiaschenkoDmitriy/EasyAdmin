<?php declare(strict_types=1);

use EAdmin\Core\Command\InitCommand;
use EAdmin\Core\Command\TSInstallCommand;
use EAdmin\Core\Component\Component;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Twig\Extensions\ScriptExtension;
use EAdmin\Core\Twig\Extensions\SlotExtension;
use EAdmin\Core\Twig\Extensions\StyleExtension;
use EAdmin\Core\Vite\ViteManifest;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Twig\Environment;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return function (ContainerConfigurator $container): void {
    $container->services()->set(Component::class)->autowire();

    $container->services()
        ->set(ViteManifest::class)
        ->args([param("kernel.project_dir") . "/public/build/.vite/manifest.json"]);

    $container->services()
        ->set(TSInstallCommand::class)
        ->args([param("kernel.project_dir")])
        ->tag('console.command');

    $container->services()
        ->set(InitCommand::class)
        ->args([param("kernel.project_dir")])
        ->tag('console.command');

    $container->services()
        ->set(ScriptExtension::class)
        ->args([service(ViteManifest::class)])
        ->tag("twig.extension");

    $container->services()
        ->set(StyleExtension::class)
        ->args([service(ViteManifest::class)])
        ->tag("twig.extension");

    $container->services()
        ->set(SlotExtension::class)
        ->args([service(ComponentRenderer::class)])
        ->tag("twig.extension");

    $container->services()
        ->set(ComponentRenderer::class)
        ->args([service(Environment::class)]);
};