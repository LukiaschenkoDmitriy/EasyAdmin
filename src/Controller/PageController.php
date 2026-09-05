<?php declare(strict_types=1);

namespace EAdmin\Core\Controller;

use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\Utils\TidyUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

abstract class PageController extends AbstractController {
    protected ComponentRenderer $renderer;

    #[Required]
    public function injectRenderer(ComponentRenderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    public function renderComponent(ComponentInterface $component, array $context = [], array $services = []): Response
    {
        return new Response(TidyUtil::cleanAndRepairHTML($this->renderer->render($component, $context, $services)), 200);
    }
}