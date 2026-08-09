<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

use EAdmin\Core\Context\ContextInterface;

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
    public function context(): ContextInterface;
    public function setContext(ContextInterface $context): self;
}