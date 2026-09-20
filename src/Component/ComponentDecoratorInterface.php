<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

interface ComponentDecoratorInterface {
    public function getDecorator(): ComponentInterface;
}