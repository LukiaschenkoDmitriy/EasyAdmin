<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\ComponentDecoratorInterface;
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
            foreach ($slots as $slot) {
                $updatedContext = $slot->beforeRender($context, $services);

                if ($updatedContext) $context = $updatedContext;
            }

            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s, $context, $services), $slots));
        }

        $updatedContext = $slots->beforeRender($context, $services);

        if ($updatedContext) $context = $updatedContext;

        return $this->renderComponent($slots, $context, $services);
    }

    private function renderComponent(ComponentInterface $component, array $context = [], array $services = []): string
    {
        $component->init();

        return $this->twig->render($component->template(), [
            "c" => $component instanceof ComponentDecoratorInterface ? $component->getDecorator() : $component, 
            "slots" => $component->slots(),
            "context" => $context,
            "services" => $services
        ]);
    }
}