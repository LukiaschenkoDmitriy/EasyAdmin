<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EAdminExtension extends AbstractExtension
{
    private array $manifestCache = [];
    public function __construct(private AssetMapperInterface $assetMapper, private string $projectDir) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_styles', [$this, 'renderStyles'], ['needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('eadmin_scripts', [$this, 'renderScripts'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function renderStyles(array $context): string
    {
        return $this->render($context, "styles");
    }

    public function renderScripts(array $context): string
    {
        return $this->render($context, "scripts");
    }

    private function render(array $context, string $format) {
        $templates = $context['_eadmin']['template'] ?? null;
        $custom = $format == "styles" ? $context["_eadmin"]["custom_styles"] : $context["_eadmin"]["custom_scripts"];

        $templates = array_values(array_unique($templates));
        $custom = array_values(array_unique($custom));

        $output = "";

        foreach($templates as $template) {
            $output .= $format == "styles" ? $this->mapStyles($template) : $this->mapScripts($template);
        }

        foreach ($custom as $value) {
            $output .= $format == "styles" ? $this->mapStyles($value) : $this->mapScripts($value);
        }

        return $output;
    }

    private function mapStyles(string $path): string
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

    private function mapScripts(string $path): string {
        $path = str_replace('@EAdmin/', '', $path);
        
        $jsFormat = "eadmin/" . preg_replace('/\.html\.twig$/', '.js', $path);
        $jsAsset = $this->assetMapper->getAsset($jsFormat);

        $scripts = "";

        if ($jsAsset) {
            $scripts .= sprintf('<script src="%s"/>', $jsAsset->publicPath);
        }

        $entryKey = "src/EAdmin/" . preg_replace('/\.html\.twig$/', '.ts', $path);
        $tsManifest = $this->getManifest();

        if (array_key_exists($entryKey, $tsManifest)) {
            $scripts .= sprintf('<script type="module" src="/build/%s"></script>', $tsManifest[$entryKey]['file']);
        }

        return $scripts;
    }

    private function getManifest(): array
    {
        if (empty($this->manifestCache)) {
            $manifestPath = $this->projectDir.'/public/build/.vite/manifest.json';
            $this->manifestCache = file_exists($manifestPath)
                ? json_decode(file_get_contents($manifestPath), true)
                : [];
        }

        return $this->manifestCache;
    }
}