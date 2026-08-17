<?php declare(strict_types=1);

namespace EAdmin\Core\DependencyInjection;

use EAdmin\Core\Assets\AssetMapperResolver;
use EAdmin\Core\Assets\AssetResolverInterface;
use EAdmin\Core\Assets\ViteAssetResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class EAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load("services.php");

        $container->setParameter("eadmin.assets.driver", $config["assets"]["driver"]);
        $container->setParameter("eadmin.assets.vite", $config["assets"]["vite"]);

        $container->setAlias(
            AssetResolverInterface::class,
            $config["assets"]["driver"] === "vite" ? ViteAssetResolver::class : AssetMapperResolver::class
        );
    }

    public function getAlias(): string
    {
        return 'eadmin';
    }
}