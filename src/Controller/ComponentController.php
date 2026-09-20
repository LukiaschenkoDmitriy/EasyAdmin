<?php declare(strict_types=1);

namespace EAdmin\Core\Controller;

use EAdmin\Core\Component\Component;
use EAdmin\Core\Component\ComponentDecoratorInterface;
use EAdmin\Core\Component\ComponentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Service\Attribute\Required;

class ComponentController extends AbstractController implements ComponentInterface, ComponentDecoratorInterface {
    private ComponentInterface $decorator;

    #[Required]
    public function di(): void {
        $this->decorator = new Component();
    }

    public function setAriaDescribedby(?string $ariaDescribedby): ComponentInterface
    {
        return $this->decorator->setAriaDescribedby($ariaDescribedby);
    }

    public function setAriaHidden(?string $ariaHidden): ComponentInterface
    {
        return $this->decorator->setAriaHidden($ariaHidden);
    }

    public function setData(array $data): ComponentInterface
    {
        return $this->decorator->setData($data);
    }

    public function setSpellcheck(?bool $spellcheck): ComponentInterface
    {
        return $this->decorator->setSpellcheck($spellcheck);
    }

    public function setAriaLabel(?string $ariaLabel): ComponentInterface
    {
        return $this->decorator->setAriaLabel($ariaLabel);
    }

    public function setAriaLabelledby(?string $ariaLabelledby): ComponentInterface
    {
        return $this->decorator->setAriaLabelledby($ariaLabelledby);
    }

    public function setAria(array $aria): ComponentInterface
    {
        return $this->decorator->setAria($aria);
    }
    
    public function setContenteditable(?bool $contentditable): ComponentInterface
    {
        return $this->decorator->setContenteditable($contentditable);
    }

    public function setId(?string $id): ComponentInterface
    {
        return $this->decorator->setId($id);
    }

    public function setStyle(?string $style): ComponentInterface
    {
        return $this->decorator->setStyle($style);
    }

    public function setTitle(?string $title): ComponentInterface
    {
        return $this->decorator->setTitle($title);
    }

    public function setTabindex(?int $tabindex): ComponentInterface
    {
        return $this->decorator->setTabindex($tabindex);
    }

    public function setDir(?string $dir): ComponentInterface
    {
        return $this->decorator->setDir($dir);
    }

    public function setRole(?string $role): ComponentInterface
    {
        return $this->decorator->setRole($role);
    }

    public function setHidden(?bool $hidden): ComponentInterface
    {
        return $this->decorator->setHidden($hidden);
    }

    public function setDraggable(?bool $draggable): ComponentInterface
    {
        return $this->decorator->setDraggable($draggable);
    }

    public function setLang(?string $lang): ComponentInterface
    {
        return $this->decorator->setLang($lang);
    }

    public function setClass(?string $class): ComponentInterface
    {
        return $this->decorator->setClass($class);
    }

    public function getId(): ?string
    {
        return $this->decorator->getId();
    }
    
    public function getClass(): ?string
    {
        return $this->decorator->getClass();
    }
    
    public function getStyle(): ?string
    {
        return $this->decorator->getStyle();
    }
    
    public function getTitle(): ?string
    {
        return $this->decorator->getTitle();
    }
    
    public function getTabindex(): ?int
    {
        return $this->decorator->getTabindex();
    }
    
    public function getLang(): ?string
    {
        return $this->decorator->getLang();
    }
    
    public function getDir(): ?string
    {
        return $this->decorator->getDir();
    }
    
    public function getRole(): ?string
    {
        return $this->decorator->getRole();
    }
    
    public function isHidden(): ?bool
    {
        return $this->decorator->isHidden();
    }
    
    public function isDraggable(): ?bool
    {
        return $this->decorator->isDraggable();
    }
    
    public function isContenteditable(): ?bool
    {
        return $this->decorator->isContenteditable();
    }
    
    public function isSpellcheck(): ?bool
    {
        return $this->decorator->isSpellcheck();
    }
    
    public function getAriaLabel(): ?string
    {
        return $this->decorator->getAriaLabel();
    }
    
    public function getAriaLabelledby(): ?string
    {
        return $this->decorator->getAriaLabelledby();
    }
    
    public function getAriaDescribedBy(): ?string
    {
        return $this->decorator->getAriaDescribedBy();
    }
    
    public function getAriaHidden(): ?string
    {
        return $this->decorator->getAriaHidden();
    }
    
    public function getData(): array
    {
        return $this->decorator->getData();
    }
    
    public function getAria(): array
    {
        return $this->decorator->getAria();
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
        return $this->decorator->getService($class);
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