<?php declare(strict_types=1);

namespace EAdmin\Core;

use EAdmin\Core\Component\Component;
use EAdmin\Core\Utils\TidyUtil;
use Twig\Environment;

class ComponentRenderer {
    public function __construct(private Environment $twig) {}

    public function render(array|Component $slots): string
    {
        if (is_array($slots)) {
            return implode("\n", array_map(fn(Component $s) => $this->twig->render($s->template(), $s->allContext()), $slots));
        }

        return $this->twig->render($slots->template(), $slots->allContext());
    }
}