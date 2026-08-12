<?php declare(strict_types=1);

namespace EAdmin\Core\Base\EComponent\Input;

use EAdmin\Core\Props\PropsInterface;
use EAdmin\Core\Props\BaseProps;

class InputProps implements PropsInterface {
    public function __construct(
        public BaseProps $base = new BaseProps(),

        public InputType $type = InputType::TEXT,
        public ?string $name = null,
        public ?string $value = null,
        public ?string $placeholder = null,

        public ?bool $required = null,
        public ?bool $disabled = null,
        public ?bool $readonly = null,
        public ?bool $autofocus = null,
        public ?bool $checked = null,
        public ?bool $multiple = null,

        public ?int $minlength = null,
        public ?int $maxlength = null,
        public ?string $min = null,
        public ?string $max = null,
        public ?string $step = null,
        public ?string $pattern = null,

        public ?string $autocomplete = null,
        public ?string $accept = null,
        public ?string $form = null,
    ){}
}