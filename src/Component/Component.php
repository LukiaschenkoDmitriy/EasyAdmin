<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

class Component implements ComponentInterface {
    private array $services = [];
    private ?string $id = null;
    private ?string $class = null;
    private ?string $style = null;

    private ?string $title = null;
    private ?int $tabindex = null;
    private ?string $lang = null;
    private ?string $dir = null;
    private ?string $role = null;

    private ?bool $hidden = null;
    private ?bool $draggable = null;
    private ?bool $contenteditable = null;
    private ?bool $spellcheck = null;

    private ?string $ariaLabel = null;
    private ?string $ariaLabelledby = null;
    private ?string $ariaDescribedby = null;
    private ?string $ariaHidden = null;

    private array $data = [];
    private array $aria = [];

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setClass(?string $class): self
    {
        $this->class = $class;
        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;
        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTabindex(?int $tabindex): self
    {
        $this->tabindex = $tabindex;
        return $this;
    }

    public function getTabindex(): ?int
    {
        return $this->tabindex;
    }

    public function setLang(?string $lang): self
    {
        $this->lang = $lang;
        return $this;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setDir(?string $dir): self
    {
        $this->dir = $dir;
        return $this;
    }

    public function getDir(): ?string
    {
        return $this->dir;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setHidden(?bool $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setDraggable(?bool $draggable): self
    {
        $this->draggable = $draggable;
        return $this;
    }

    public function isDraggable(): ?bool
    {
        return $this->draggable;
    }

    public function setContenteditable(?bool $contenteditable): self
    {
        $this->contenteditable = $contenteditable;
        return $this;
    }

    public function isContenteditable(): ?bool
    {
        return $this->contenteditable;
    }

    public function setSpellcheck(?bool $spellcheck): self
    {
        $this->spellcheck = $spellcheck;
        return $this;
    }

    public function isSpellcheck(): ?bool
    {
        return $this->spellcheck;
    }

    public function setAriaLabel(?string $ariaLabel): self
    {
        $this->ariaLabel = $ariaLabel;
        return $this;
    }

    public function getAriaLabel(): ?string
    {
        return $this->ariaLabel;
    }

    public function setAriaLabelledby(?string $ariaLabelledby): self
    {
        $this->ariaLabelledby = $ariaLabelledby;
        return $this;
    }

    public function getAriaLabelledby(): ?string
    {
        return $this->ariaLabelledby;
    }

    public function setAriaDescribedby(?string $ariaDescribedby): self
    {
        $this->ariaDescribedby = $ariaDescribedby;
        return $this;
    }

    public function getAriaDescribedBy(): ?string
    {
        return $this->ariaDescribedby;
    }

    public function setAriaHidden(?string $ariaHidden): self
    {
        $this->ariaHidden = $ariaHidden;
        return $this;
    }

    public function getAriaHidden(): ?string
    {
        return $this->ariaHidden;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setAria(array $aria): self
    {
        $this->aria = $aria;
        return $this;
    }

    public function getAria(): array
    {
        return $this->aria;
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

    public function init(array $context): ?array
    {
        return null;
    }

    public function beforeRender(array $context, array $services): array
    {
        $this->services = $services;
        $contextChanges = $this->init($context);

        if ($contextChanges != null) return $contextChanges;

        return $context;
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