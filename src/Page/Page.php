<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

use EAdmin\Core\Component\Component;
use EAdmin\Core\Component\ComponentDecoratorInterface;
use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\Controller\PageController;
use Symfony\Contracts\Service\Attribute\Required;

class Page extends PageController implements PageInterface, ComponentInterface, ComponentDecoratorInterface {
    private ComponentInterface $decorator;

    #[Required]
    public function boot(): void
    {
        $this->decorator = new Component();
        $this->init();
    }

    public function services(): array
    {
        return [];
    }

    public function context(): array
    {
        return [];
    }

    public function alias(): ?string
    {
        return $this->decorator->alias();
    }

    public function template(): string
    {
        return $this->decorator->template();
    }

    public function beforeRender(array $context, array $services): ?array
    {
        return $this->decorator->beforeRender($context, $services);
    }

    public function getService(string $class): mixed
    {
        return $this->getService($class);
    }

    public function init(): void
    {
        $this->decorator->init();
    }

    public function styles(): array
    {
        return $this->decorator->styles();
    }

    public function scripts(): array
    {
        return $this->decorator->scripts();
    }

    public function slots(): array|ComponentInterface
    {
        return $this->decorator->slots();
    }

    public function setSlots(array|ComponentInterface $slots): ComponentInterface
    {
        return $this->decorator->setSlots($slots);
    }

    public function updateContext(array $context): ?array
    {
        return $this->decorator->updateContext($context);
    }

    public function getDecorator(): ComponentInterface
    {
        return $this->decorator;
    }
}