<?php declare(strict_types=1);

namespace EAdmin\Core\Page;

use EAdmin\Core\Component\Component;

abstract class Page extends Component {
    abstract public function title(): string;
    
    public function favicon(): ?string {
        return null;
    }

    public function context(): array
    {
        return [
            "title" => $this->title(),
            "favicon" => $this->favicon()
        ];
    }
}