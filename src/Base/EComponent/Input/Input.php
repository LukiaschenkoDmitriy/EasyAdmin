<?php declare(strict_types=1);

namespace EAdmin\Core\Base\EComponent\Input;

use EAdmin\Core\Component\Component;

class Input extends Component {
    public function __construct(InputProps $props)
    {
        $this->setProps($props);
    }
}