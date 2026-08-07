<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

abstract class Component {
    public function __construct(protected array $context = [], protected array $slots = []) {}
    public function alias(): ?string
    {
        return null;
    }

    public function template(): string
    {
        $necessaryFile = preg_replace('/\.php$/', '.html.twig', (new \ReflectionClass(static::class))->getFileName()); 

        if (!file_exists($necessaryFile)) {
            throw new \Exception("File: " . $necessaryFile . " necessary to create the component.");
        }

        return preg_replace('/\.php$/', '.html.twig', "@EAdmin" . explode("EAdmin", (new \ReflectionClass(static::class))->getFileName())[1]);
    }

    public function context(): array
    {
        return [];
    }

    /** @return array<string> */
    public function styles(): array
    {
        return [];
    }

    /** @return array<string> */
    public function scripts(): array
    {
        return [];
    }

    /** @return array<Component> */
    public function slots(): array
    {
        return $this->slots;
    }

    public function setSlots(array $slots): void
    {
        $this->slots = $slots;
    }

    public function allContext(): array
    {
        return [
            "_eadmin" => [
                "template" => $this->getRecursiveTemplates($this),
                "custom_styles" => $this->getRecursiveStyles($this),
                "custom_scripts" => $this->getRecursiveScripts($this)
            ],
            ...array_merge($this->context, $this->context()),
            "slots" => $this->slots()
        ];
    }

    private function getRecursiveTemplates(Component $component): array
    {
        return array_merge([$component->template()], ...array_map(fn(Component $slot) => $this->getRecursiveTemplates($slot), $component->slots()));
    }

    private function getRecursiveStyles(Component $component): array
    {
        return array_merge($component->styles(), ...array_map(fn(Component $slot) => $this->getRecursiveStyles($slot), $component->slots()));;
    }

    private function getRecursiveScripts(Component $component): array
    {
        return array_merge($component->scripts(), ...array_map(fn(Component $slot) => $this->getRecursiveScripts($slot), $component->slots()));;
    }
}