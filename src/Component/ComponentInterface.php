<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

interface ComponentInterface
{
    public function setId(?string $id): self;

    public function getId(): ?string;

    public function setClass(?string $class): self;

    public function getClass(): ?string;

    public function setStyle(?string $style): self;

    public function getStyle(): ?string;

    public function setTitle(?string $title): self;

    public function getTitle(): ?string;

    public function setTabindex(?int $tabindex): self;

    public function getTabindex(): ?int;

    public function setLang(?string $lang): self;

    public function getLang(): ?string;

    public function setDir(?string $dir): self;

    public function getDir(): ?string;

    public function setRole(?string $role): self;

    public function getRole(): ?string;

    public function setHidden(?bool $hidden): self;

    public function isHidden(): ?bool;

    public function setDraggable(?bool $draggable): self;

    public function isDraggable(): ?bool;

    public function setContenteditable(?bool $contenteditable): self;

    public function isContenteditable(): ?bool;

    public function setSpellcheck(?bool $spellcheck): self;

    public function isSpellcheck(): ?bool;

    public function setAriaLabel(?string $ariaLabel): self;

    public function getAriaLabel(): ?string;

    public function setAriaLabelledby(?string $ariaLabelledby): self;

    public function getAriaLabelledby(): ?string;

    public function setAriaDescribedby(?string $ariaDescribedby): self;

    public function getAriaDescribedBy(): ?string;

    public function setAriaHidden(?string $ariaHidden): self;

    public function getAriaHidden(): ?string;

    public function setData(array $data): self;

    public function getData(): array;

    public function setAria(array $aria): self;

    public function getAria(): array;

    public function alias(): ?string;

    public function template(): string;

    /** @return array<string> */
    public function styles(): array;

    /** @return array<string> */
    public function scripts(): array;

    /** @return array<ComponentInterface>|ComponentInterface */
    public function slots(): array|ComponentInterface;

    public function setSlots(array|ComponentInterface $slots): self;

    public function init(): void;

    public function updateContext(array $context): array|null;

    public function beforeRender(array $context, array $services): array|null;

    public function getService(string $class): mixed;
}