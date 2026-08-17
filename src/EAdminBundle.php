<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Assets\AssetMapperResolver;
use EAdmin\Core\Assets\AssetResolverInterface;
use EAdmin\Core\Assets\ViteAssetResolver;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Override;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class EAdminBundle extends AbstractBundle {
    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter("kernel.project_dir");
        $workDirectory = !file_exists($projectDir . "/src/EAdmin") ? __DIR__ : "%kernel.project_dir%/src/EAdmin";

        if ($container->hasExtension("framework")) {
            $container->prependExtensionConfig("framework", [
                "asset_mapper" => [
                    "paths" => [
                        $workDirectory => "eadmin",
                    ]
                ]
            ]);
        }

        if ($container->hasExtension("twig")) {
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    $workDirectory => 'EAdmin',
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

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('driver')
                            ->values(['vite', 'asset_mapper'])
                            ->defaultValue('asset_mapper')
                        ->end()
                        ->arrayNode('vite')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('typescript')->defaultTrue()->end()
                                ->booleanNode('scss')->defaultTrue()->end()
                                ->booleanNode('tailwind')->defaultFalse()->end()
                                ->scalarNode('manifest_path')
                                    ->defaultValue('%kernel.project_dir%/public/build/.vite/manifest.json')
                                ->end()
                                ->scalarNode('build_public_path')->defaultValue('/build/')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}