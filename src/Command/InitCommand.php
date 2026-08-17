<?php declare(strict_types=1);

namespace EAdmin\Core\Command;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class InitCommand extends AbstractMaker
{
    public function __construct(private readonly string $kernelDir) {}

    public static function getCommandName(): string
    {
        return 'eadmin:install';
    }

    public static function getCommandDescription(): string
    {
        return 'Install EAdmin';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void {}

    public function configureDependencies(DependencyBuilder $dependencies): void {}

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $fs = new Filesystem();
        $targetDir = $this->kernelDir . '/src/EAdmin';

        if (!$fs->exists($targetDir)) {
            $fs->mkdir($targetDir);
            $io->success(sprintf('Work directory "%s" created.', $targetDir));
        }

        $driver = $io->choice('Which asset pipeline do you want to use?', [
            'vite' => 'Vite (TS/SCSS/Tailwind, requires a build step)',
            'asset_mapper' => 'Symfony AssetMapper (no build step)',
        ], 'vite');

        $driverKey = array_search($driver, [
            'vite' => 'Vite (TS/SCSS/Tailwind, requires a build step)',
            'asset_mapper' => 'Symfony AssetMapper (no build step)',
        ], true) ?: $driver;

        $config = [
            'assets' => [
                'driver' => $driverKey,
            ],
        ];

        if ($driverKey === 'vite') {
            $config['assets']['vite'] = $this->setupVite($io, $fs);
        }

        $this->writeConfig($io, $fs, $config);

        $io->success('EAdmin has been installed. Please review config/packages/eadmin.yaml.');
    }

    private function setupVite(ConsoleStyle $io, Filesystem $fs): array
    {
        $typescript = $io->confirm('Enable TypeScript?', true);
        $scss       = $io->confirm('Enable SCSS?', true);
        $tailwind   = $io->confirm('Enable Tailwind CSS?', false);

        $bundleDir  = dirname(__DIR__); // Core/
        $projectDir = $this->kernelDir;
        $stubsDir   = $bundleDir . '/Resources/stubs/vite';

        $this->copyStub(
            $io,
            $fs,
            $stubsDir . '/package.json.tpl.php',
            $projectDir . '/package.json'
        );

        if ($typescript) {
            $this->copyStub(
                $io,
                $fs,
                $stubsDir . '/tsconfig.json.tpl.php',
                $projectDir . '/tsconfig.json'
            );
        }

        $this->generateViteConfig($io, $fs, $stubsDir, $projectDir, $typescript, $scss, $tailwind);

        $deps = [];
        if ($tailwind) {
            $deps['tailwindcss']      = '^4.0.0';
            $deps['@tailwindcss/vite'] = '^4.0.0';
        }
        if ($scss) {
            $deps['sass-embedded'] = '^1.80.0';
        }

        if ($deps) {
            $this->mergeNpmDeps($projectDir, $deps, $io);
        }

        if ($io->confirm('Install npm dependencies now?', true)) {
            $this->runNpmInstall($projectDir, $io);
        }

        return [
            'typescript'        => $typescript,
            'scss'              => $scss,
            'tailwind'          => $tailwind,
            'manifest_path'     => '%kernel.project_dir%/public/build/.vite/manifest.json',
            'build_public_path' => '/build/',
        ];
    }

    /**
     * Generates vite.config.ts from the stub template.
     * The template already contains the full logic; we only inject
     * optional Tailwind plugin import/registration when needed.
     */
    private function generateViteConfig(
        ConsoleStyle $io,
        Filesystem $fs,
        string $stubsDir,
        string $projectDir,
        bool $typescript,
        bool $scss,
        bool $tailwind
    ): void {
        $targetPath = $projectDir . '/vite.config.ts';

        if ($fs->exists($targetPath) && !$io->confirm('File "vite.config.ts" already exists. Overwrite?', false)) {
            $io->note('Skipped "vite.config.ts".');
            return;
        }

        $stubPath = $stubsDir . '/vite.config.ts.tpl.php';
        if (!$fs->exists($stubPath)) {
            $io->error(sprintf('Stub not found: %s', $stubPath));
            return;
        }

        // Load the stub as a plain string (it is a .tpl.php but contains pure JS)
        $content = file_get_contents($stubPath);

        if ($tailwind) {
            // Inject Tailwind import and plugin
            $content = str_replace(
                "import { defineConfig } from 'vite';\n",
                "import { defineConfig } from 'vite';\nimport tailwindcss from '@tailwindcss/vite';\n",
                $content
            );

            $content = str_replace(
                "export default defineConfig({\n",
                "export default defineConfig({\n    plugins: [tailwindcss()],\n",
                $content
            );
        }

        // Optional: strip TypeScript / SCSS entry generation if the user disabled them.
        // For simplicity the stub always generates both; we can refine later if needed.

        $fs->dumpFile($targetPath, $content);
        $io->success('Generated "vite.config.ts".');
    }

    private function copyStub(ConsoleStyle $io, Filesystem $fs, string $sourcePath, string $targetPath): void
    {
        if ($fs->exists($targetPath) && !$io->confirm(sprintf('File "%s" already exists. Overwrite?', basename($targetPath)), false)) {
            $io->note(sprintf('Skipped "%s".', basename($targetPath)));
            return;
        }

        $fs->copy($sourcePath, $targetPath, true);
        $io->success(sprintf('Copied "%s" → "%s"', basename($sourcePath), basename($targetPath)));
    }

    private function mergeNpmDeps(string $projectDir, array $deps, ConsoleStyle $io): void
    {
        $packageJsonPath = $projectDir . '/package.json';
        if (!file_exists($packageJsonPath)) {
            return;
        }

        $packageJson = json_decode(file_get_contents($packageJsonPath), true, 512, JSON_THROW_ON_ERROR);
        $packageJson['devDependencies'] = array_merge($packageJson['devDependencies'] ?? [], $deps);

        file_put_contents(
            $packageJsonPath,
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );

        $io->note('Added dependencies to package.json: ' . implode(', ', array_keys($deps)));
    }

    private function runNpmInstall(string $projectDir, ConsoleStyle $io): void
    {
        $io->section('Installing npm dependencies...');

        $process = new Process(['npm', 'install'], $projectDir);
        $process->setTimeout(300);
        $process->mustRun(function (string $type, string $buffer) use ($io): void {
            $io->write($buffer);
        });

        $io->success('Dependencies installed.');
    }

    private function writeConfig(ConsoleStyle $io, Filesystem $fs, array $config): void
    {
        $configDir  = $this->kernelDir . '/config/packages';
        $configPath = $configDir . '/eadmin.yaml';

        if (!$fs->exists($configDir)) {
            $fs->mkdir($configDir);
        }

        $fs->dumpFile($configPath, Yaml::dump(['eadmin' => $config], 6, 4));
        $io->success(sprintf('Configuration written to "%s".', $configPath));
    }
}