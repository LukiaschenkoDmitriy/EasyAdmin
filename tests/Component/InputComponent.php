<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Component;

use EAdmin\Core\Component\Component;
use Override;

class InputComponent extends Component {
    public function __construct(string $placeholder)
    {
        return parent::__construct(
            ["placeholder" => $placeholder]
        );
    }
    public function template(): string
    {
        return "@EAdmin/components/Input/index.html.twig";
    }
}