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
    public function slots(): array;
    public function setSlots(array $slots): self;
    public function context(): array;
    public function setContext(array $context): self;
    public function allContext(): array;
}