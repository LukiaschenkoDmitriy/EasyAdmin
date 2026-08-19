<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Props\BaseProps;
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
        /**
         * @var BaseProps $props
         */
        $props = $context["baseProps"];

        $attrs = [];

        $simple = [
            "id"        => $props->id,
            "class"     => $props->class,
            "style"     => $props->style,
            "title"     => $props->title,
            "tabindex"  => $props->tabindex,
            "lang"      => $props->lang,
            "dir"       => $props->dir,
            "role"      => $props->role,
        ];

        foreach ($simple as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, (string) $value);
            }
        }

        $booleans = [
            "hidden"          => $props->hidden,
            "draggable"       => $props->draggable,
            "contenteditable" => $props->contenteditable,
            "spellcheck"      => $props->spellcheck,
        ];

        foreach ($booleans as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, $value ? "true" : "false");
            }
        }

        $aria = [
            "aria-label"       => $props->ariaLabel,
            "aria-labelledby"  => $props->ariaLabelledby,
            "aria-describedby" => $props->ariaDescribedby,
            "aria-hidden"      => $props->ariaHidden,
        ];

        foreach ($aria as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, (string) $value);
            }
        }

        foreach ($props->data as $key => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr("data-{$key}", (string) $value);
            }
        }

        foreach ($props->aria as $key => $value) {
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