<?php declare(strict_types=1);

namespace EAdmin\Core\Component;

final class ComponentHelper {
    public static function getTemplate(string $class): string
    {
        $filePath = (new \ReflectionClass($class))->getFileName();
        $necessaryFile = preg_replace('/\.php$/', '.html.twig', $filePath);

        if (!file_exists($necessaryFile)) {
            throw new \Exception("File: " . $necessaryFile . " necessary to create the component.");
        }

        $marker = '/EAdmin/';
        $pos = strrpos($filePath, $marker);

        if ($pos === false) {
            throw new \Exception("Could not resolve EAdmin-relative path for: " . $filePath);
        }

        $relativePath = substr($filePath, $pos + strlen($marker));

        return preg_replace('/\.php$/', '.html.twig', '@EAdmin/' . $relativePath);
    }


    public static function getRecursiveTemplates(ComponentInterface $component): array
    {
        return array_merge([$component->template()], ...array_map(fn(ComponentInterface $slot) => ComponentHelper::getRecursiveTemplates($slot), $component->slots()));
    }

    public static function getRecursiveStyles(ComponentInterface $component): array
    {
        return array_merge($component->styles(), ...array_map(fn(ComponentInterface $slot) => ComponentHelper::getRecursiveStyles($slot), $component->slots()));;
    }

    public static function getRecursiveScripts(ComponentInterface $component): array
    {
        return array_merge($component->scripts(), ...array_map(fn(ComponentInterface $slot) => ComponentHelper::getRecursiveScripts($slot), $component->slots()));;
    }
}