<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

interface ComponentInterface extends ComponentAttributesInterface, ComponentPluginInterface
{

    public function alias(): ?string;

    public function template(): string;

    /** @return array<string> */
    public function styles(): array;

    /** @return array<string> */
    public function scripts(): array;

    /** @return array<ComponentInterface>|ComponentInterface */
    public function slots(): array|ComponentInterface;

    public function setSlots(array|ComponentInterface $slots): self;

    public function init(array $context): ?array;

    public function beforeRender(array $context, array $services): ?array;

    public function getService(string $class): mixed;
}