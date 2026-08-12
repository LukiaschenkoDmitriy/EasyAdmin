<?php declare(strict_types=1);

namespace EAdmin\Core\Base\EComponent\Input;

use EAdmin\Core\Props\PropsInterface;

class InputProps implements PropsInterface {
    public function __construct(
        public InputType $type = InputType::TEXT
    ){}
}