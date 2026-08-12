<?php declare(strict_types=1);

namespace EAdmin\Core\Props;

class BaseProps implements PropsInterface {
    public function __construct(
        public ?string $id = null,
        public ?string $class = null,
        public ?string $style = null,

        public ?string $title = null,
        public ?int $tabindex = null,
        public ?string $lang = null,
        public ?string $dir = null,
        public ?string $role = null,

        public ?bool $hidden = null,
        public ?bool $draggable = null,
        public ?bool $contenteditable = null,
        public ?bool $spellcheck = null,

        public ?string $ariaLabel = null,
        public ?string $ariaLabelledby = null,
        public ?string $ariaDescribedby = null,
        public ?string $ariaHidden = null,

        public array $data = [],
        public array $aria = [],
    ) { }
}