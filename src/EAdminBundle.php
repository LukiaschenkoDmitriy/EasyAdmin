<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\DependencyInjection\EAdminExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class EAdminBundle extends AbstractBundle {
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EAdminExtension();
    }

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
    }
}