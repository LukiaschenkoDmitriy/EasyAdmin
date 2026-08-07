<?php declare(strict_types=1);

namespace EAdmin\Core\Controller;

use EAdmin\Core\Component\Component;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Utils\TidyUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class ComponentController extends AbstractController {
    public function __construct(private ComponentRenderer $renderer) {}

    public function renderComponent(Component $component): Response
    {
        $view = TidyUtil::cleanAndRepairHTML($this->renderer->render($component));
        return new Response($view, 200);
    }
}