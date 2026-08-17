<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Assets\AssetResolverInterface;
use EAdmin\Core\Component\ComponentHelper;
use EAdmin\Core\Component\ComponentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StyleExtension extends AbstractExtension
{
    public function __construct(private readonly AssetResolverInterface $resolver) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_styles', [$this, 'index'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function index(array $context): string
    {
        /** @var ComponentInterface $component */
        $component = $context['_self'];

        $paths = [
            ...ComponentHelper::getRecursiveTemplates($component),
            ...ComponentHelper::getRecursiveStyles($component),
        ];

        $output = '';
        foreach (array_unique($paths) as $path) {
            $path = str_replace('@EAdmin/', '', $path);
            foreach ($this->resolver->resolve($path)->styles as $href) {
                $output .= sprintf('<link rel="stylesheet" href="%s">', $href);
            }
        }

        return $output;
    }
}