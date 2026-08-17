<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\ComponentHelper;
use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\Vite\ViteManifest;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StyleExtension extends AbstractExtension
{
    public function __construct(private readonly ViteManifest $manifest) {}

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

        $templates = array_values(array_unique(ComponentHelper::getRecursiveTemplates($component)));
        $styles = array_values(array_unique(ComponentHelper::getRecursiveStyles($component)));

        $output = '';

        foreach ($templates as $template) {
            $output .= $this->render($template);
        }

        foreach ($styles as $style) {
            $output .= $this->render($style);
        }

        return $output;
    }

    private function render(string $path): string
    {
        $path = str_replace('@EAdmin/', '', $path);
        $basePath = preg_replace('/\.html\.twig$/', '', $path);

        $links = '';

        $tsEntry = $this->manifest->entry('src/EAdmin/' . $basePath . '.ts');
        foreach ($tsEntry?->css ?? [] as $cssFile) {
            $links .= sprintf('<link rel="stylesheet" href="%s">', $cssFile);
        }

        $scssEntry = $this->manifest->entry('src/EAdmin/' . $basePath . '.scss');
        if ($scssEntry !== null) {
            $links .= sprintf('<link rel="stylesheet" href="%s">', $scssEntry->file);
        }

        return $links;
    }
}