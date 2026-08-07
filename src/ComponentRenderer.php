<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\ComponentInterface;
use Twig\Environment;

class ComponentRenderer {
    public function __construct(private Environment $twig) {}

    public function render(array|ComponentInterface $slots): string
    {
        if (is_array($slots)) {
            return implode("\n", array_map(fn(ComponentInterface $s) => $this->twig->render($s->template(), $s->allContext()), $slots));
        }

        return $this->twig->render($slots->template(), $slots->allContext());
    }
}