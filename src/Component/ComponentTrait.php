<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

use EAdmin\Core\Props\PropsInterface;

trait ComponentTrait {
    public ?PropsInterface $props = null;
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

    public function props(): PropsInterface|array|null
    {
        return $this->props;
    }

    public function setProps(PropsInterface|array|null $props): self
    {
        $this->props = $props;
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