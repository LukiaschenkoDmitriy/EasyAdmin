<?php declare(strict_types=1);

namespace EAdmin\Core\DependencyInjection\Compiler;

use EAdmin\Core\Attribute\AsComponentController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Attribute\Route;

class ComponentControllerCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $routes = [];

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if ($class === null || !class_exists($class)) {
                continue;
            }

            $reflection = $container->getReflectionClass($class);
            if ($reflection === null) {
                continue;
            }

            if (empty($reflection->getAttributes(AsComponentController::class))) {
                continue;
            }

            $routeAttributes = $reflection->getAttributes(Route::class);
            if (empty($routeAttributes)) {
                continue;
            }

            foreach ($routeAttributes as $routeAttribute) {
                /** @var Route $route */
                $route = $routeAttribute->newInstance();

                $routes[] = [
                    'class'   => $class,
                    'name'    => $route->name ?? $this->generateRouteName($class),
                    'path'    => $route->path,
                    'methods' => $route->methods
                ];
            }

            $definition->setPublic(true);
        }

        $container->setParameter('eadmin.component_controller_routes', $routes);
    }

    private function generateRouteName(string $class): string
    {
        return 'eadmin_component_' . strtolower(str_replace('\\', '_', $class));
    }
}