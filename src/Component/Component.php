<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

class Component implements ComponentInterface {
    use ComponentTrait;

    public static string $CUSTOM_COMPONENT_TAG = "custom_component";

    public static function createComponent(string $dryHTML): ComponentInterface
    {
        return new class($dryHTML) extends Component {
            public function __construct(public string $html) {}

            public function template(): string
            {
                return static::$CUSTOM_COMPONENT_TAG;
            }
        };
    }
}