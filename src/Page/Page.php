<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

use EAdmin\Core\Component\ComponentTrait;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;

abstract class Page extends PageController implements PageInterface {
    use ComponentTrait;

    public function __construct(protected ComponentRenderer $renderer)
    {
        parent::__construct($renderer);
        $this->init();
    }

    public function __invoke(): Response
    {
        return $this->renderComponent($this);
    }
}