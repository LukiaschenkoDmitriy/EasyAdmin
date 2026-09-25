<?php declare(strict_types=1);

namespace EAdmin\Core\Command;

use EAdmin\Core\ElasticSearch\Attribute\SearchableEntity;
use EAdmin\Core\ElasticSearch\IndexManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

#[AsCommand(name: 'eadmin:elastic:index')]
class SyncSearchIndexesCommand extends Command
{
    public function __construct(private string $kernelDir, private IndexManager $indexManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();
        $finder->files()->in($this->kernelDir . '/Entity')->name('*.php');

        foreach ($finder as $file) {
            $class = 'App\\Entity\\' . $file->getFilenameWithoutExtension();

            if (!class_exists($class)) {
                continue;
            }

            $reflection = new \ReflectionClass($class);
            if (empty($reflection->getAttributes(SearchableEntity::class))) {
                continue;
            }

            $this->indexManager->createOrUpdateIndex($class);
            $output->writeln("Sync indexes for class: $class");
        }

        return Command::SUCCESS;
    }
}