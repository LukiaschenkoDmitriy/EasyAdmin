<?php declare(strict_types=1);

namespace EAdmin\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder("eadmin");
        $root = $treeBuilder->getRootNode();

        $root
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

        return $treeBuilder;
    }
}