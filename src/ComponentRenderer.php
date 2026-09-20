<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\Page\PageInterface;
use Twig\Environment;

class ComponentRenderer {
    public function __construct(private Environment $twig) {}

    public function render(array|ComponentInterface $slots, array $context = [], array $services = []): string
    {
        $services = $slots instanceof PageInterface ? array_merge($slots->services(), $services) : $services;
        $context = $slots instanceof PageInterface ? array_merge($slots->context(), $context) : $context;

        if (is_array($slots)) {
            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s, $context, $services), $slots));
        }

        return $this->renderComponent($slots, $context, $services);
    }

    private function renderComponent(ComponentInterface $component, array $context = [], array $services = []): string
    {
        $context = $component->beforeRender($context, $services);

        return $this->twig->render($component->template(), [
            "c" => $component, 
            "slots" => $component->slots(),
            "context" => $context,
            "services" => $services
        ]);
    }
}