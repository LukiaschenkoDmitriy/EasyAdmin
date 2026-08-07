<?php declare(strict_types=1);

namespace EAdmin\Core\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'eadmin:ts:install', description: 'Copy EAdmin config files (vite.config.ts, tsconfig.json) into the project root')]
class TSInstallCommand extends Command {
    public function __construct(private readonly KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filesystem = new Filesystem();

        $projectDir = $this->kernel->getProjectDir();
        $bundleDir = dirname(__DIR__);

        $filesToCopy = [
            'Resources/stubs/vite.config.ts' => 'vite.config.ts',
            'Resources/stubs/tsconfig.json'  => 'tsconfig.json',
            'Resources/stubs/package.json'  => 'package.json',
        ];

        foreach ($filesToCopy as $source => $target) {
            $sourcePath = $bundleDir . '/' . $source;
            $targetPath = $projectDir . '/' . $target;

            if ($filesystem->exists($targetPath)) {
                if (!$io->confirm(sprintf('File "%s" already exists. Overwrite?', $target), false)) {
                    $io->note(sprintf('Skipped "%s".', $target));
                    continue;
                }
            }

            $filesystem->copy($sourcePath, $targetPath, true);
            $io->success(sprintf('Copied "%s" -> "%s"', $source, $target));
        }

        if (!$io->confirm('Do you want install node packages?', true)) {
            $io->note('Skipped.');
        } else {
            $io->section('Installing npm dependencies...');

            $process = new Process(['npm', 'install'], $projectDir);
            $process->setTimeout(150);

            $process->mustRun(function (string $type, string $buffer) use ($output): void {
                $output->write($buffer);
            });

            $io->success('Dependencies installed.');
        }

        return Command::SUCCESS;
    }

}