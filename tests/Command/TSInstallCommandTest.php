<?php declare(strict_types=1);

namespace EAdmin\Core\Tests\Command;

use EAdmin\Core\Command\TSInstallCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class TSInstallCommandTest extends TestCase
{
    private string $projectDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->projectDir = sys_get_temp_dir() . '/eadmin_ts_install_' . uniqid();
        $this->filesystem->mkdir($this->projectDir);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->projectDir);
    }

    public function testCopiesFilesAndSkipsNpmInstall(): void
    {
        $command = new TSInstallCommand($this->projectDir);

        $application = new Application();
        $application->addCommand($command);

        $tester = new CommandTester($application->find('eadmin:ts:install'));
        $tester->setInputs(['no']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);

        $this->assertFileExists($this->projectDir . '/vite.config.ts');
        $this->assertFileExists($this->projectDir . '/tsconfig.json');
        $this->assertFileExists($this->projectDir . '/package.json');

        $this->assertStringContainsString('Skipped.', $tester->getDisplay());
    }

    public function testAsksBeforeOverwritingExistingFile(): void
    {
        file_put_contents($this->projectDir . '/vite.config.ts', 'old content');

        $command = new TSInstallCommand($this->projectDir);

        $application = new Application();
        $application->addCommand($command);

        $tester = new CommandTester($application->find('eadmin:ts:install'));

        $tester->setInputs(['no', 'no']);

        $exitCode = $tester->execute([]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringEqualsFile($this->projectDir . '/vite.config.ts', 'old content');
        $this->assertStringContainsString('Skipped "vite.config.ts"', $tester->getDisplay());
    }
}