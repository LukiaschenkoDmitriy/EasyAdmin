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

        $context = $this->updateContext($slots, $context, $services);

        if (is_array($slots)) {
            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s, $context, $services), $slots));
        }

        return $this->renderComponent($slots, $context, $services);
    }

    private function updateContext(array|ComponentInterface $slots, array $context, array $services): array
    {
        if (is_array($slots)) {
            /** @var ComponentInterface $component */
            foreach ($slots as $component) {
                $newContext = $component->beforeRender($context, $services);

                if (!$newContext) continue;
                $context = $newContext;
            }

            return $context;
        }

        return $slots->beforeRender($context, $services) ?? $context;
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