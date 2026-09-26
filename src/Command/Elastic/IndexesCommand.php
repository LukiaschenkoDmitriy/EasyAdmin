<?php declare(strict_types=1);

namespace EAdmin\Core\Command\Elastic;

use EAdmin\Core\Service\ElasticService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'eadmin:elastic:index')]
class IndexesCommand extends Command
{
    public function __construct(private ElasticService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument("entity", null, "Create, update, delete selected entity");
        $this->addOption("delete", "d", InputOption::VALUE_NONE, "Delete index");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entity = $input->getArgument("entity");
        $deleteMode = $input->getOption("delete");

        $function = [$this->service, $deleteMode ? "deleteIndexes" : "createOrUpdateIndexes"];

        foreach($function($entity) as $message) {
            $output->writeln($message);
        }

        return Command::SUCCESS;
    }
}