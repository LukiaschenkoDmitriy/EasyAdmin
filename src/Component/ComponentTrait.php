<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

trait ComponentTrait {
    public ?string $id = null;
    public ?string $class = null;
    public ?string $style = null;

    public ?string $title = null;
    public ?int $tabindex = null;
    public ?string $lang = null;
    public ?string $dir = null;
    public ?string $role = null;

    public ?bool $hidden = null;
    public ?bool $draggable = null;
    public ?bool $contenteditable = null;
    public ?bool $spellcheck = null;

    public ?string $ariaLabel = null;
    public ?string $ariaLabelledby = null;
    public ?string $ariaDescribedby = null;
    public ?string $ariaHidden = null;

    public array $data = [];
    public array $aria = [];

    public function __construct()
    {
        $this->init();
    }

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

    public function init(): void
    {
        return;
    }
}