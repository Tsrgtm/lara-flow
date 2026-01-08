<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Symfony\Component\Process\Process;

class PrepareDeployment extends Command
{
    protected $signature = 'app:prepare-deploy
        {--with-composer : Run composer install before packaging}
        {--skip-npm : Skip npm build}
        {--output=deploy.zip : Output zip file name}';

    protected $description = 'Build and package the application for deployment';

    public function handle(): int
    {
        $this->info('🚀 Preparing application for deployment...');

        $rootPath  = base_path();
        $buildPath = storage_path('app/deploy_build');
        $zipPath   = base_path($this->option('output'));

        // Cleanup previous build
        $this->deleteDirectory($buildPath);
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        mkdir($buildPath, 0755, true);

        // 1️⃣ Frontend build
        if (!$this->option('skip-npm')) {
            $this->info('📦 Running npm build...');
            if (!is_dir(base_path('node_modules'))) {
                $this->runProcess(['npm', 'install']);
            }
            $this->runProcess(['npm', 'run', 'build']);
        } else {
            $this->comment('⏭️  Skipping npm build');
        }

        // 2️⃣ Composer (OPT-IN ONLY)
        if ($this->option('with-composer')) {
            $this->info('📦 Running composer install...');
            $this->runProcess([
                'composer',
                'install',
                '--no-dev',
                '--optimize-autoloader',
                '--no-interaction',
            ]);
        } else {
            $this->comment('⏭️  Composer install skipped (default)');
        }

        // 3️⃣ Laravel optimization
        $this->info('⚡ Optimizing Laravel...');
        $this->callSilent('config:clear');
        $this->callSilent('config:cache');
        $this->callSilent('route:cache');
        $this->callSilent('view:cache');

        // 4️⃣ Copy project files
        $this->info('📁 Preparing build directory...');
        $this->copyProject($rootPath, $buildPath);

        // 5️⃣ Zip everything
        $this->info('🗜️  Creating deployment zip...');
        $this->zipDirectory($buildPath, $zipPath);

        // 6️⃣ Cleanup
        $this->deleteDirectory($buildPath);

        $this->info("✅ Deployment package ready: {$zipPath}");

        return Command::SUCCESS;
    }

    /**
     * Run shell commands safely
     */
    protected function runProcess(array $command): void
    {
        $this->line('> ' . implode(' ', $command));

        $process = new Process($command, base_path());
        $process->setTimeout(null);
        $process->run(fn ($type, $buffer) => print $buffer);

        if (!$process->isSuccessful()) {
            $this->error('❌ Command failed');
            exit(1);
        }
    }

    /**
     * Copy project excluding unnecessary files
     */
    protected function copyProject(string $source, string $destination): void
    {
        $exclude = [
            'node_modules',
            '.git',
            '.idea',
            '.vscode',
            '.gitignore',
            '.env.example',
            '.junie',
            'tests',
            'storage/logs',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/app/deploy_build',
        ];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $normalizedPath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);

            foreach ($exclude as $excluded) {
                if (str_starts_with($normalizedPath, $excluded)) {
                    continue 2;
                }
            }

            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($file->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                // ✅ Ensure parent directory exists
                if (!is_dir(dirname($targetPath))) {
                    mkdir(dirname($targetPath), 0755, true);
                }

                copy($file->getPathname(), $targetPath);
            }
        }
    }

    /**
     * Zip directory recursively (FIXED)
     */
    protected function zipDirectory(string $source, string $zipFile): void
    {
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create zip file.');
        }

        $source = realpath($source);

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $filePath);

            // ✅ ZIP requires forward slashes
            $relativePath = str_replace('\\', '/', $relativePath);

            if ($file->isDir()) {
                // ✅ Explicitly create directory in zip
                $zip->addEmptyDir($relativePath);
            } else {
                // ✅ File goes inside already-created directory
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }


    /**
     * Delete directory recursively
     */
    protected function deleteDirectory(?string $dir): void
    {
        if (!$dir || !is_dir($dir)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }

        rmdir($dir);
    }
}
