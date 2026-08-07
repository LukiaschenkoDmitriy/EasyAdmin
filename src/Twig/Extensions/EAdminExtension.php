<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EAdminExtension extends AbstractExtension
{
    public function __construct(private AssetMapperInterface $assetMapper) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_styles', [$this, 'renderStyles'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('eadmin_scripts', [$this, 'renderScripts'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function renderStyles(array $context): string
    {
        return $this->render($context, "css");
    }

    public function renderScripts(array $context): string
    {
        return $this->render($context, "js");
    }

    private function render(array $context, string $format) {
        $templates = $context['_eadmin']['template'] ?? null;
        $custom = $format == "css" ? $context["_eadmin"]["custom_styles"] : $context["_eadmin"]["custom_scripts"];

        $templates = array_values(array_unique($templates));
        $custom = array_values(array_unique($custom));

        $output = "";

        foreach($templates as $template) {
            $output .= $template ? $this->map($template, $format) : "";
        }

        foreach ($custom as $value) {
            $output .= $this->map($value, $format);
        }

        return $output;
    }

    private function map(string $path, string $format): string
    {
        $path = str_replace('@EAdmin/', '', $path);
        $path = preg_replace('/\.html\.twig$/', '.'.$format, $path);
        $path = "eadmin/" . $path;

        $asset = $this->assetMapper->getAsset($path);

        if (!$asset) return "";

        $output = $format == "css" ? sprintf('<link rel="stylesheet" href="%s">', $asset->publicPath) : sprintf('<script src="%s"/>', $asset->publicPath);

        return $asset ? $output : '';
    }
}