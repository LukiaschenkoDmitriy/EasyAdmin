<?php declare(strict_types=1);

namespace EAdmin\Core\Service;

use Doctrine\ORM\EntityManagerInterface;
use EAdmin\Core\ElasticSearch\Attribute\SearchableEntity;
use EAdmin\Core\ElasticSearch\IndexManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ElasticService {
    public function __construct(private string $kernelDir, private IndexManager $index, private EntityManagerInterface $manager) { }
    public function createOrUpdateIndexes(?string $entity = null): iterable
    {
        return $this->loopEntities(fn(string $class) => $this->createOrUpdateIndex($class), $entity);
    }

    public function deleteIndexes(?string $entity = null): iterable
    {
        return $this->loopEntities(fn(string $class) => $this->deleteOne($class), $entity);
    }

    public function bulkIndexes(?string $entity = null): iterable {
        return $this->loopEntities(fn(string $class) => $this->bulkOne($class), $entity);
    }

    private function loopEntities(callable $callback, ?string $entity = null): iterable
    {
        foreach(static::getEntities($this->kernelDir) as $class) {
            if (!$entity) {
                $message = $callback($class);
                if ($message) yield $message;
            }

            if ($entity && $this->getClassName($class) == $entity) {
                $message = $callback($class);
                if ($message) yield $message;
            }
        }
    }

    private function createOrUpdateIndex(string $class): ?string {
        $attribute = static::getSearchableAttribute($class);

        if (!$attribute) return null;

        $this->index->createOrUpdateIndex($class);
        return "Indexes have been created for {$attribute->indexName}";
    }

    private function bulkOne(string $class): ?string {
        $attribute = static::getSearchableAttribute($class);

        if (!$attribute) return null;

        $entities = $this->manager->getRepository($class)->findAll();
        $count = $this->index->bulkIndex(
            $attribute->indexName,
            $entities,
            fn ($entity) => $entity->getId(),
        );

        return "Indexed {$count} records in {$attribute->indexName}";
    }

    private function deleteOne(string $class): ?string {
        $attribute = static::getSearchableAttribute($class);

        if (!$attribute) return null;

        $index = $this->index->getClient()->getIndex($attribute->indexName);

        if ($index->exists()) {
            $index->delete();
            return "Index {$attribute->indexName} was deleted";
        }

        return null;
    }

    private static function getEntities(string $kernelDir): array
    {
        $finder = new Finder();
        $finder->files()->in($kernelDir . '/src/Entity')->name('*.php');

        return array_map(fn(SplFileInfo $file) => 'App\\Entity\\' . $file->getFilenameWithoutExtension(), iterator_to_array($finder->getIterator()));
    }

    private static function getSearchableAttribute(string $class): ?SearchableEntity {
        $reflection = new \ReflectionClass($class);
        $attrs = $reflection->getAttributes(SearchableEntity::class);
        if (empty($attrs)) {
            return null;
        }

        return $attrs[0]->newInstance();
    }

    private static function getClassName(string $class): string {
        return (new \ReflectionClass($class))->getShortName();
    }
}