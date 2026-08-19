<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AttributeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction("eadmin_attr", [$this, "index"], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function index(array $context): string
    {
        $component = $context["c"];

        $attrs = [];

        $simple = [
            "id"        => $component->id,
            "class"     => $component->class,
            "style"     => $component->style,
            "title"     => $component->title,
            "tabindex"  => $component->tabindex,
            "lang"      => $component->lang,
            "dir"       => $component->dir,
            "role"      => $component->role,
        ];

        foreach ($simple as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, (string) $value);
            }
        }

        $booleans = [
            "hidden"          => $component->hidden,
            "draggable"       => $component->draggable,
            "contenteditable" => $component->contenteditable,
            "spellcheck"      => $component->spellcheck,
        ];

        foreach ($booleans as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, $value ? "true" : "false");
            }
        }

        $aria = [
            "aria-label"       => $component->ariaLabel,
            "aria-labelledby"  => $component->ariaLabelledby,
            "aria-describedby" => $component->ariaDescribedby,
            "aria-hidden"      => $component->ariaHidden,
        ];

        foreach ($aria as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, (string) $value);
            }
        }

        foreach ($component->data as $key => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr("data-{$key}", (string) $value);
            }
        }

        foreach ($component->aria as $key => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr("aria-{$key}", (string) $value);
            }
        }

        return $attrs === [] ? "" : " " . implode(" ", $attrs);
    }

    private function renderAttr(string $name, string $value): string
    {
        return sprintf('%s="%s"', $name, htmlspecialchars($value, ENT_QUOTES, "UTF-8"));
    }
}