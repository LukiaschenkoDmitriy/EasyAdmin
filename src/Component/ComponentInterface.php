<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

interface ComponentInterface {
    public function template(): string;
    public function alias(): ?string;
    /** @return array<string> */
    public function styles(): array;
    /** @return array<string> */
    public function scripts(): array;
    /** @return array<ComponentInterface> */
    public function slots(): array|ComponentInterface;
    public function setSlots(array|ComponentInterface $slots): self;
    public function init(): void;
    public function beforeRender(array $context, array $services): array|null;
    public function updateContext(array $context): array|null;
    public function getService(string $class): mixed;
    public function setId(?string $id): self;
    public function setClass(?string $class): self;
    public function setStyle(?string $style): self;
    public function setTitle(?string $title): self;
    public function setTabindex(?int $tabindex): self;
    public function setLang(?string $lang): self;
    public function setDir(?string $dir): self;
    public function setRole(?string $role): self;
    public function setHidden(?bool $hidden): self;
    public function setDraggable(?bool $draggable): self;
    public function setContenteditable(?bool $contentditable): self;
    public function setSpellcheck(?bool $spellcheck): self;
    public function setAriaLabel(?string $ariaLabel): self;
    public function setAriaLabelledby(?string $ariaLabelledby): self;
    public function setAriaDescribedby(?string $ariaDescribedby): self;
    public function setAriaHidden(?string $ariaHidden): self;
    public function setData(array $data): self;
    public function setAria(array $aria): self;
}