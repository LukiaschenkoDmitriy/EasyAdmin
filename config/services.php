<?php declare(strict_types=1);

use EAdmin\Core\Assets\AssetMapperResolver;
use EAdmin\Core\Assets\ViteAssetResolver;
use EAdmin\Core\Command\Elastic\BulkIndexesCommand;
use EAdmin\Core\Command\Elastic\IndexesCommand;
use EAdmin\Core\Command\InitCommand;
use EAdmin\Core\Component\Component;
use EAdmin\Core\ComponentRenderer;
use EAdmin\Core\ElasticSearch\DocumentExtractor;
use EAdmin\Core\ElasticSearch\ElasticSearchFactory;
use EAdmin\Core\ElasticSearch\IndexManager;
use EAdmin\Core\ElasticSearch\MappingBuilder;
use EAdmin\Core\Repository\DoctrineRepository;
use EAdmin\Core\Repository\ElasticSearchRepository;
use EAdmin\Core\Service\ElasticService;
use EAdmin\Core\Twig\Extensions\AttributeExtension;
use EAdmin\Core\Twig\Extensions\ScriptExtension;
use EAdmin\Core\Twig\Extensions\SlotExtension;
use EAdmin\Core\Twig\Extensions\StyleExtension;
use EAdmin\Core\Vite\ViteManifest;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container): void {
    $services = $container->services()->defaults()->autoconfigure()->autowire();

    $services->set(Component::class);

    $services->set(AssetMapperResolver::class);
    $services->set(ViteAssetResolver::class)->arg('$viteConfig', param("eadmin.assets.vite"));
    $services->set(ViteManifest::class)->arg('$viteConfig', param("eadmin.assets.vite"));

    $services->set(InitCommand::class)->args([param("kernel.project_dir")])->tag('make.command');
    $services->set(IndexesCommand::class)->tag("console.command");
    $services->set(BulkIndexesCommand::class)->tag("console.command");

    $services->set(DocumentExtractor::class);
    $services->set(ElasticSearchFactory::class);
    $services->set(DoctrineRepository::class);  
    $services->set(ElasticSearchRepository::class);
    $services->set(IndexManager::class);
    $services->set(MappingBuilder::class);
    $services->set(ElasticService::class)->arg('$kernelDir', param("kernel.project_dir"));

    $services->set(ScriptExtension::class)->tag("twig.extension");
    $services->set(StyleExtension::class)->tag("twig.extension");
    $services->set(SlotExtension::class)->tag("twig.extension");
    $services->set(AttributeExtension::class)->tag("twig.extension");

    $services->set(ComponentRenderer::class);
};