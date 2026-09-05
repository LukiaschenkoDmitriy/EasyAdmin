<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

use EAdmin\Core\Component\ComponentTrait;
use EAdmin\Core\Controller\PageController;
use Symfony\Contracts\Service\Attribute\Required;

abstract class Page extends PageController implements PageInterface {
    use ComponentTrait;

    #[Required]
    public function boot(): void
    {
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
}