<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\ComponentRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SlotExtension extends AbstractExtension
{
    public function __construct(private ComponentRenderer $renderer) {}

    public function getFunctions()
    {
        return [
            new TwigFunction('slot', [$this, 'render'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction("render", [$this, "dryRender"], ['needs_context' => true, "is_safe" => ["html"]])
        ];
    }

    public function dryRender(array $context, ComponentInterface|array|null $slot = null): string
    {
        if ($slot == null) {
            return $this->renderer->render($context["slots"], array_key_exists("context", $context) ? $context["context"] : []);
        }

        return $this->renderer->render($slot);
    }

    public function render(array $context, string|int $slot, int $position = 0): string
    {
        $slots = $context["slots"];

        if (empty($slots)) "";

        $currentSlot = null;

        if (is_numeric($slot)) {
            $currentSlot = $slots[$slot];
        } else {
            $currentSlot = array_filter($slots, fn(ComponentInterface $s) => $s->alias() == $slot)[$position];
        }

        if (!$currentSlot) return "";

        return $this->renderer->render($currentSlot, array_key_exists("context", $context) ? $context["context"] : []);
    }
}