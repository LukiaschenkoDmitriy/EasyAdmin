<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Fixture;

use EAdmin\Core\EAdminBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel {
    public function registerBundles(): iterable
    {
        return [new FrameworkBundle(), new EAdminBundle()];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => 'test',
        ]);
    }

}