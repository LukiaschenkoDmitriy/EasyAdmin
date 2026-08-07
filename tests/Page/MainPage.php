<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Page;

use EAdmin\Core\Page\Page;
use EAdmin\Core\Tests\Component\DashboardComponent;

class MainPage extends Page {
    public function title(): string {
        return "Hello world";
    }

    public function template(): string
    {
        return "@EAdmin/pages/Main/index.html.twig";
    }

    public function slots(): array
    {
        return [
            new DashboardComponent(["text" => "dashboard 11"]),
            new DashboardComponent(["text" => "dashboard 22"])
        ];
    }
}