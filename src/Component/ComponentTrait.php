<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

use EAdmin\Core\Context\ContextInterface;

trait ComponentTrait {
    public ContextInterface $context;
    /**
     * @var array<ComponentInterface>
     */
    private array $slots = [];
    public function alias(): ?string
    {
        return null;
    }

    public function template(): string
    {
        return ComponentHelper::getTemplate(static::class);
    }

    public function context(): ContextInterface
    {
        return $this->context;
    }

    public function setContext(ContextInterface $context): self
    {
        $this->context = $context;
        return $this;
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

    public function setSlots(array $slots): self
    {
        $this->slots = $slots;
        return $this;
    }
}