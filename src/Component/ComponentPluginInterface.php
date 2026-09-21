<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

interface ComponentPluginInterface {
    public function plugin(array $context): ?string;
    public function pluginData(array $context): ?array;
}