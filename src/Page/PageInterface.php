<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

use EAdmin\Core\Component\ComponentInterface;
use Symfony\Component\HttpFoundation\Response;

interface PageInterface extends ComponentInterface {
    public function __invoke(): Response;
}