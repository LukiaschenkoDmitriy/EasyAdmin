<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\Component;
use EAdmin\Core\Component\ComponentInterface;
use Twig\Environment;

class ComponentRenderer {
    public function __construct(private Environment $twig) {}

    public function render(array|ComponentInterface $slots, array $context = []): string
    {
        if (is_array($slots)) {
            foreach ($slots as $slot) {
                $slot->beforeRender($context);
            }

            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s, $context), $slots));
        }

        $slots->beforeRender($context);
        return $this->renderComponent($slots, $context);
    }

    private function renderComponent(ComponentInterface $component, array $context = []): string
    {
        if ($component->template() == Component::$CUSTOM_COMPONENT_TAG) {
            return $component->html;
        }

        return $this->twig->render($component->template(), [
            "c" => $component, 
            "slots" => $component->slots(),
            "context" => $context
        ]);
    }
}