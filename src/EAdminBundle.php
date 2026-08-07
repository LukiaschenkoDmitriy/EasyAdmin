<?php declare(strict_types=1);

namespace EAdmin\Core;

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
                        "%kernel.project_dir%/EAdmin" => "eadmin",
                    ]
                ]
            ]);
        }

        if ($container->hasExtension("twig")) {
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    '%kernel.project_dir%/EAdmin' => 'EAdmin',
                ],
            ]);
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        if (class_exists(\Twig\Environment::class)) {
            $configurator->import(__DIR__ . "/../config/services.php");
        }
    }
}