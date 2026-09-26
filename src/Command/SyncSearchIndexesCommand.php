<?php declare(strict_types=1);

namespace EAdmin\Core\Command;

use Doctrine\ORM\EntityManagerInterface;
use EAdmin\Core\ElasticSearch\Attribute\SearchableEntity;
use EAdmin\Core\ElasticSearch\IndexManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'eadmin:elastic:index')]
class SyncSearchIndexesCommand extends Command
{
    public function __construct(private string $kernelDir, private IndexManager $indexManager, private EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('fresh', null, InputOption::VALUE_NONE, 'Update indexes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fresh = $input->getOption('fresh');
        $finder = new Finder();
        $finder->files()->in($this->kernelDir . '/src/Entity')->name('*.php');

        foreach ($finder as $file) {
            $class = 'App\\Entity\\' . $file->getFilenameWithoutExtension();

            $reflection = new \ReflectionClass($class);
            $attrs = $reflection->getAttributes(SearchableEntity::class);
            if (empty($attrs)) {
                continue;
            }

            /** @var SearchableEntity $searchable */
            $searchable = $attrs[0]->newInstance();

            if ($fresh) {
                $index = $this->indexManager->getClient()->getIndex($searchable->indexName);
                if ($index->exists()) {
                    $index->delete();
                    $output->writeln("✗ Індекс {$searchable->indexName} видалено");
                }
            }

            $this->indexManager->createOrUpdateIndex($class);
            $output->writeln("✓ Мапінг створено для {$searchable->indexName}");

            $entities = $this->manager->getRepository($class)->findAll();
            $count = $this->indexManager->bulkIndex(
                $searchable->indexName,
                $entities,
                fn ($entity) => $entity->getId(),
            );

            $output->writeln("✓ Проіндексовано {$count} записів у {$searchable->indexName}");
        }

        return Command::SUCCESS;
    }
}