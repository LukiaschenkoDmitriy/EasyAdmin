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
    private const DRIVER_CHOICES = [
        'vite' => 'Vite (TS/SCSS/Tailwind, requires a build step)',
        'asset_mapper' => 'Symfony AssetMapper (no build step)',
    ];

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

        $driver = $io->choice('Which asset pipeline do you want to use?', self::DRIVER_CHOICES, 'vite');
        $driverKey = array_search($driver, self::DRIVER_CHOICES, true) ?: $driver;

        $config = [
            'assets' => [
                'driver' => $driverKey,
            ],
        ];

        if ($driverKey === 'vite') {
            $config['assets']['vite'] = $this->setupVite($io, $fs, $generator);
        }

        $this->writeConfig($io, $fs, $config);

        $io->success('EAdmin has been installed. Please review config/packages/eadmin.yaml.');
    }

    private function setupVite(ConsoleStyle $io, Filesystem $fs, Generator $generator): array
    {
        $typescript = $io->confirm('Enable TypeScript?', true);
        $scss       = $io->confirm('Enable SCSS?', true);
        $tailwind   = $io->confirm('Enable Tailwind CSS?', false);

        $bundleDir = dirname(__DIR__); // Core/
        $stubsDir  = $bundleDir . '/Resources/stubs/vite';

        $this->generateStub($io, $fs, $generator, $stubsDir . '/package.json.tpl.php', 'package.json');

        if ($typescript) {
            $this->generateStub($io, $fs, $generator, $stubsDir . '/global.d.ts.tpl.php', 'src/EAdmin/global.d.ts');
            $this->generateStub($io, $fs, $generator, $stubsDir . '/tsconfig.json.tpl.php', 'tsconfig.json');
        }

        $this->generateStub($io, $fs, $generator, $stubsDir . '/vite.config.ts.tpl.php', 'vite.config.ts');

        if ($tailwind) {
            $this->generateStub($io, $fs, $generator, $stubsDir . '/postcss.config.cjs.tpl.php', 'postcss.config.cjs');
        }

        // Flush queued files to disk now — mergeNpmDeps() below needs to
        // read package.json back from the filesystem.
        $generator->writeChanges();

        $deps = [];
        if ($scss) {
            $deps['sass-embedded'] = '^1.80.0';
        }
        if ($tailwind) {
            $deps['tailwindcss']          = '^4.0.0';
            $deps['@tailwindcss/postcss'] = '^4.0.0';
            $deps['postcss']              = '^8.4.0';
        }

        if ($deps) {
            $this->mergeNpmDeps($this->kernelDir, $deps, $io);
        }

        if ($tailwind) {
            $io->note('Tailwind is wired through PostCSS, so it now also processes your .scss entries — add `@import "tailwindcss";` to your main stylesheet.');
        }

        if ($io->confirm('Install npm dependencies now?', true)) {
            $this->runNpmInstall($this->kernelDir, $io);
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
     * Queues a stub for generation via the maker Generator, preserving the
     * same "already exists → ask to overwrite" behavior the old
     * Filesystem::copy()-based copyStub() had.
     *
     * $relativeTargetPath is relative to $this->kernelDir (i.e. the project root).
     */
    private function generateStub(
        ConsoleStyle $io,
        Filesystem $fs,
        Generator $generator,
        string $stubPath,
        string $relativeTargetPath,
        array $variables = []
    ): void {
        if (!$fs->exists($stubPath)) {
            $io->error(sprintf('Stub not found: %s', $stubPath));
            return;
        }

        $absoluteTargetPath = $this->kernelDir . '/' . $relativeTargetPath;

        if ($fs->exists($absoluteTargetPath)
            && !$io->confirm(sprintf('File "%s" already exists. Overwrite?', $relativeTargetPath), false)
        ) {
            $io->note(sprintf('Skipped "%s".', $relativeTargetPath));
            return;
        }

        // Absolute stub path -> Generator treats it as a direct template
        // file rather than resolving it against a bundle skeleton dir.
        $generator->generateFile($relativeTargetPath, $stubPath, $variables);
        $io->success(sprintf('Generated "%s".', $relativeTargetPath));
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