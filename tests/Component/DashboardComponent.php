<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Component;

use EAdmin\Core\Component\Component;

class DashboardComponent extends Component {
    public function alias(): ?string
    {
        return "Dashboard";
    }

    public function template(): string
    {
        return "@EAdmin/components/Dashboard/index.html.twig";
    }

    public function slots(): array
    {
        return [
            new InputComponent("Test placeholder")
        ];
    }
}