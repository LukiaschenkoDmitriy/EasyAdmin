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
            foreach ($slots as $component) {
                $context = $this->updateContext($component, $context, $services);
            }

            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s, $context, $services), $slots));
        }

        $context = $this->updateContext($slots, $context, $services);

        return $this->renderComponent($slots, $context, $services);
    }

    public function updateContext(ComponentInterface $component, array $context, array $services): array
    {
        return $component->beforeRender($context, $services) ?? $context;
    }

    private function renderComponent(ComponentInterface $component, array $context = [], array $services = []): string
    {
        return $this->twig->render($component->template(), [
            "c" => $component, 
            "slots" => $component->slots(),
            "context" => $context,
            "services" => $services
        ]);
    }
}