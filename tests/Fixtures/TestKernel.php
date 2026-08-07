<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Fixtures;

use EAdmin\Core\EAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new EAdminBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('framework', [
                'secret' => 'test',
                'test' => true,
                'asset_mapper' => [
                    'paths' => ['assets/'],
                ],
            ]);
        });
    }

    public function getCacheDir(): string
    {
        return dirname(__DIR__) . '/eadmin_bundle_cache/' . spl_object_hash($this);
    }

    public function getLogDir(): string
    {
        return dirname(__DIR__) . '/eadmin_bundle_logs';
    }
}