<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\ComponentInterface;
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
         * @var ComponentInterface $component
         */
        $component = $context["c"];

        $attrs = [];

        $simple = [
            "id"        => $component->getId(),
            "class"     => $component->getClass(),
            "style"     => $component->getStyle(),
            "title"     => $component->getTitle(),
            "tabindex"  => $component->getTabindex(),
            "lang"      => $component->getLang(),
            "dir"       => $component->getDir(),
            "role"      => $component->getRole(),
        ];

        foreach ($simple as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, (string) $value);
            }
        }

        $booleans = [
            "hidden"          => $component->isHidden(),
            "draggable"       => $component->isDraggable(),
            "contenteditable" => $component->isContenteditable(),
            "spellcheck"      => $component->isSpellcheck(),
        ];

        foreach ($booleans as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, $value ? "true" : "false");
            }
        }

        $aria = [
            "aria-label"       => $component->getAriaLabel(),
            "aria-labelledby"  => $component->getAriaLabelledby(),
            "aria-describedby" => $component->getAriaDescribedBy(),
            "aria-hidden"      => $component->getAriaHidden(),
        ];

        foreach ($aria as $name => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr($name, (string) $value);
            }
        }

        foreach ($component->getData() as $key => $value) {
            if ($value !== null) {
                $attrs[] = $this->renderAttr("data-{$key}", (string) $value);
            }
        }

        foreach ($component->getAria() as $key => $value) {
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