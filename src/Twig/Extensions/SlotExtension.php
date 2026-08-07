<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\Component;
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
            new TwigFunction("render", [$this, "dryRender"], ["is_safe" => ["html"]])
        ];
    }

    public function dryRender(Component|array $slot): string
    {
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
            $currentSlot = array_filter($slots, fn(Component $s) => $s->alias() == $slot)[$position];
        }

        if (!$currentSlot) return "";

        return $this->renderer->render($currentSlot);
    }
}