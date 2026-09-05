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
    public function beforeRender(array $context): array|null;
    public function updateContext(array $context): array|null;
    public function getService(string $class): mixed;
}