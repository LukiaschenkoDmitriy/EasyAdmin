<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Assets\AssetResolverInterface;
use EAdmin\Core\Component\ComponentHelper;
use EAdmin\Core\Component\ComponentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptExtension extends AbstractExtension
{
    public function __construct(private readonly AssetResolverInterface $resolver) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_scripts', [$this, 'index'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function index(array $context): string
    {
        /** @var ComponentInterface $component */
        $component = $context['c'];

        $paths = [
            ...ComponentHelper::getRecursiveTemplates($component),
            ...ComponentHelper::getRecursiveScripts($component),
        ];

        $output = '';
        foreach (array_unique($paths) as $path) {
            $path = str_replace('@EAdmin/', '', $path);
            foreach ($this->resolver->resolve($path)->scripts as $src) {
                $output .= sprintf('<script type="module" src="%s"></script>', $src);
            }
        }

        return $output;
    }
}