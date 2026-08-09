<?php declare(strict_types=1);

namespace EAdmin\Core\Twig\Extensions;

use EAdmin\Core\Component\ComponentHelper;
use EAdmin\Core\Component\ComponentInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptExtension extends AbstractExtension {
    private array $manifestCache = [];
    public function __construct(private AssetMapperInterface $assetMapper, private string $projectDir) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('eadmin_scripts', [$this, 'index'], ['needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    public function index(array $context): string
    {
        /**
         * @var ComponentInterface $component
         */
        $component = $context["_self"];
        
        $templates = array_values(array_unique(ComponentHelper::getRecursiveTemplates($component)));
        $scripts = array_values(array_unique(ComponentHelper::getRecursiveScripts($component)));

        $output = "";

        foreach($templates as $template) {
            $output .= $this->map($template);
        }

        foreach ($scripts as $value) {
            $output .= $this->map($value);
        }

        return $output;
    }

    private function map(string $path): string {
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