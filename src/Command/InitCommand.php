<?php declare(strict_types=1);

namespace EAdmin\Core\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'eadmin:init', description: 'Init EAdmin command')]
class InitCommand extends Command
{
    public function __construct(private readonly string $kernelDir) {
        parent::__construct();
     }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetDir = $this->kernelDir . "/src/EAdmin";

        $fs = new Filesystem();

        if ($fs->exists($targetDir)) {
            return Command::SUCCESS;
        }

        $fs->mkdir($targetDir);

        return Command::SUCCESS;
    }
}