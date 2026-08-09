<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\ComponentHelper;
use EAdmin\Core\Component\ComponentInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StyleExtension extends AbstractExtension {
    public function __construct(private AssetMapperInterface $assetMapper) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_styles', [$this, 'index'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function index(array $context): string
    {
        /**
         * @var ComponentInterface $component
         */
        $component = $context["_self"];

        $templates = array_values(array_unique(ComponentHelper::getRecursiveTemplates($component)));
        $styles = array_values(array_unique(ComponentHelper::getRecursiveStyles($component)));

        $output = "";

        foreach($templates as $template) {
            $output .= $this->map($template);
        }

        foreach ($styles as $value) {
            $output .= $this->map($value);
        }

        return $output;
    }

    private function map(string $path): string
    {
        $path = str_replace('@EAdmin/', '', $path);
        
        $cssFormat = "eadmin/" . preg_replace('/\.html\.twig$/', '.css', $path);
        $scssFormat = "eadmin/" . preg_replace('/\.html\.twig$/', '.scss', $path);

        $cssAsset = $this->assetMapper->getAsset($cssFormat);
        $scssAsset = $this->assetMapper->getAsset($scssFormat);

        $styles = "";

        if ($cssAsset) {
            $styles .= sprintf('<link rel="stylesheet" href="%s">', $cssAsset->publicPath);
        }

        if ($scssAsset) {
            $styles .= sprintf('<link rel="stylesheet" href="%s">', $scssAsset->publicPath);
        }

        return $styles;
    }
}