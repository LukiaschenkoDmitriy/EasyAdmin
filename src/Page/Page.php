<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

use EAdmin\Core\Component\ComponentTrait;
use EAdmin\Core\Controller\ComponentController;
use Symfony\Component\HttpFoundation\Response;

abstract class Page extends ComponentController implements PageInterface {
    use ComponentTrait;

    public function __invoke(): Response
    {
        return $this->renderComponent($this);
    }
}