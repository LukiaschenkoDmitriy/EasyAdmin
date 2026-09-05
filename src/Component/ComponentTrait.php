<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

use Symfony\Component\Console\Formatter\NullOutputFormatterStyle;

trait ComponentTrait {
    public array $services = [];
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

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;
        return $this;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;
        return $this;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setTabindex(?int $tabindex): self
    {
        $this->tabindex = $tabindex;
        return $this;
    }

    public function setLang(?string $lang): self
    {
        $this->lang = $lang;
        return $this;
    }

    public function setDir(?string $dir): self
    {
        $this->dir = $dir;
        return $this;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function setHidden(?bool $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function setDraggable(?bool $draggable): self
    {
        $this->draggable = $draggable;
        return $this;
    }

    public function setContenteditable(?bool $contenteditable): self
    {
        $this->contenteditable = $contenteditable;
        return $this;
    }

    public function setSpellcheck(?bool $spellcheck): self
    {
        $this->spellcheck = $spellcheck;
        return $this;
    }

    public function setAriaLabel(?string $ariaLabel): self
    {
        $this->ariaLabel = $ariaLabel;
        return $this;
    }

    public function setAriaLabelledby(?string $ariaLabelledby): self
    {
        $this->ariaLabelledby = $ariaLabelledby;
        return $this;
    }

    public function setAriaDescribedby(?string $ariaDescribedby): self
    {
        $this->ariaDescribedby = $ariaDescribedby;
        return $this;
    }

    public function setAriaHidden(?string $ariaHidden): self
    {
        $this->ariaHidden = $ariaHidden;
        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setAria(array $aria): self
    {
        $this->aria = $aria;
        return $this;
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

    public function updateContext(array $context): array|null
    {
        return null;
    }

    public function beforeRender(array $context): array|null
    {
        $this->services = $context["services"];
        return $this->updateContext($context);
    }

    public function getService(string $class): mixed
    {
        if (!$this->services) return null;

        foreach ($this->services as $service) {
            if ($service::class === $class) {
                return $service;
            }
        }

        return null;
    }
}