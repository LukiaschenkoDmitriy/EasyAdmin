<?php declare(strict_types=1);

namespace EAdmin\Core\Controller;

use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Utils\TidyUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class PageController extends AbstractController {
    public function __construct(protected ComponentRenderer $renderer) {}

    public function renderComponent(ComponentInterface $component): Response
    {
        $view = TidyUtil::cleanAndRepairHTML($this->renderer->render($component));
        return new Response($view, 200);
    }
}