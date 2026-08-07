<?php declare(strict_types=1);

namespace EAdmin\Core\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ComponentControllerLoader extends Loader
{
    private bool $loaded = false;

    public function __construct(private readonly array $routes)
    {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the "eadmin_component" loader twice.');
        }

        $collection = new RouteCollection();

        foreach ($this->routes as $route) {
            $collection->add($route['name'], new Route(
                $route['path'],
                defaults: ['_controller' => $route['class']],
                methods: $route['methods'],
            ));
        }

        $this->loaded = true;

        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return $type === 'eadmin_component';
    }
}