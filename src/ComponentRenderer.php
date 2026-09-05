<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\Component;
use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\Page\PageInterface;
use Twig\Environment;

class ComponentRenderer {
    public function __construct(private Environment $twig) {}

    public function render(array|ComponentInterface $slots, array $context = []): string
    {
        if (is_array($slots)) {
            foreach ($slots as $slot) {
                $updatedContext = $slot->beforeRender($context);

                if ($updatedContext) $context = $updatedContext;
            }

            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s, $context), $slots));
        }

        $updatedContext = $slots->beforeRender($context);

        if ($updatedContext) $context = $updatedContext;

        return $this->renderComponent($slots, $context);
    }

    private function renderComponent(ComponentInterface $component, array $context = []): string
    {
        if ($component->template() == Component::$CUSTOM_COMPONENT_TAG) {
            return $component->html;
        }

        return $this->twig->render($component->template(), array_merge([
            "c" => $component, 
            "slots" => $component->slots(),
            "context" => $context
        ], $component instanceof PageInterface ? ["services" => $component->services()] : []));
    }
}