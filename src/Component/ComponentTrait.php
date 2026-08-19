<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

use EAdmin\Core\Props\BaseProps;
use EAdmin\Core\Props\PropsInterface;

trait ComponentTrait {
    public PropsInterface|array|null $props = null;
    public ?PropsInterface $baseProps = null;
    /**
     * @var array<ComponentInterface>|ComponentInterface
     */
    private array|ComponentInterface $slots = [];
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

    /** @return array<ComponentInterface>|ComponentInterface */
    public function slots(): array|ComponentInterface
    {
        return $this->slots;
    }

    public function setSlots(array|ComponentInterface $slots): self
    {
        $this->slots = $slots;
        return $this;
    }

    public function baseProps(): PropsInterface
    {
        if ($this->baseProps == null) {
            $this->baseProps = new BaseProps();
        }

        return $this->baseProps;
    }

    public function setBaseProps(PropsInterface $props): self
    {
        $this->baseProps = $props;
        return $this;
    }
}