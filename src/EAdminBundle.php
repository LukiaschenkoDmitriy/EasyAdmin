<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Assets\AssetMapperResolver;
use EAdmin\Core\Assets\AssetResolverInterface;
use EAdmin\Core\Assets\ViteAssetResolver;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class EAdminBundle extends AbstractBundle {
    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        if ($container->hasExtension("framework")) {
            $container->prependExtensionConfig("framework", [
                "asset_mapper" => [
                    "paths" => [
                        "%kernel.project_dir%/src/EAdmin" => "eadmin",
                    ]
                ]
            ]);
        }

        if ($container->hasExtension("twig")) {
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    '%kernel.project_dir%/src/EAdmin' => 'EAdmin',
                ],
            ]);
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import(__DIR__ . "/../config/services.php");

        $container->setParameter("eadmin.assets.driver", $config["assets"]["driver"]);
        $container->setParameter("eadmin.assets.vite", $config["assets"]["vite"]);

        $container->setAlias(
            AssetResolverInterface::class,
            $config["assets"]["driver"] === "vite" ? ViteAssetResolver::class : AssetMapperResolver::class
        );
    }
}