<?php declare(strict_types=1);

use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Routing\ComponentControllerLoader;
use EAdmin\Core\Twig\Extensions\EAdminExtension;
use EAdmin\Core\Twig\Extensions\SlotExtension;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Twig\Environment;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return function (ContainerConfigurator $container): void {
    $container->services()
        ->set(ComponentControllerLoader::class)
        ->args([param('eadmin.component_controller_routes')])
        ->tag("routing.loader");

    $container->services()
        ->set(EAdminExtension::class)
        ->args([service(AssetMapperInterface::class)])
        ->tag("twig.extension");

    $container->services()
        ->set(SlotExtension::class)
        ->args([service(ComponentRenderer::class)])
        ->tag("twig.extension");

    $container->services()
        ->set(ComponentRenderer::class)
        ->args([service(Environment::class)]);
};