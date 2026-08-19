<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\Component;
use EAdmin\Core\Component\ComponentInterface;
use Twig\Environment;

class ComponentRenderer {
    public function __construct(private Environment $twig) {}

    public function render(array|ComponentInterface $slots): string
    {
        if (is_array($slots)) {
            return implode("\n", array_map(fn(ComponentInterface $s) => $this->renderComponent($s), $slots));
        }

        return $this->renderComponent($slots);
    }

    private function renderComponent(ComponentInterface $component): string
    {
        if ($component->template() == Component::$CUSTOM_COMPONENT_TAG) {
            return $component->props()["html"];
        }

        return $this->twig->render($component->template(), [
            "_self" => $component, 
            "props" => $component->props(), 
            "slots" => $component->slots(),
            "baseProps" => $component->baseProps()
        ]);
    }
}