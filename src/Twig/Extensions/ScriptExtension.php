<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\ComponentHelper;
use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\Vite\ViteManifest;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptExtension extends AbstractExtension
{
    public function __construct(private readonly ViteManifest $manifest) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_scripts', [$this, 'index'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function index(array $context): string
    {
        /** @var ComponentInterface $component */
        $component = $context['_self'];

        $templates = array_values(array_unique(ComponentHelper::getRecursiveTemplates($component)));
        $scripts = array_values(array_unique(ComponentHelper::getRecursiveScripts($component)));

        $output = '';

        foreach ($templates as $template) {
            $output .= $this->render($template);
        }

        foreach ($scripts as $script) {
            $output .= $this->render($script);
        }

        return $output;
    }

    private function render(string $path): string
    {
        $path = str_replace('@EAdmin/', '', $path);
        $entryKey = 'src/EAdmin/' . preg_replace('/\.html\.twig$/', '.ts', $path);

        $entry = $this->manifest->entry($entryKey);

        if ($entry === null) {
            return '';
        }

        return sprintf('<script type="module" src="%s"></script>', $entry->file);
    }
}