<?php declare(strict_types=1);

namespace EAdmin\Core\Command\Elastic;

use EAdmin\Core\Service\ElasticService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: "eadmin:elastic:bulk", description: "Bulk elasticsearch indexes")]
class BulkIndexesCommand extends Command {
    public function __construct(private ElasticService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument("entity", null, "Builk selected entity", null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entity = $input->getArgument("entity");

        foreach($this->service->bulkIndexes($entity) as $message) {
            $output->writeln($message);
        }

        return Command::SUCCESS;
    }
}