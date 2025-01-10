<?php

namespace Native\Laravel\Commands\Traits;

trait CleansEnvFile
{
    protected function prepareNativeEnv(): void
    {
        $this->line('Preparing production .env file…');

        $envFile = app()->environmentFilePath();

        if (! file_exists($backup = $this->getBackupEnvFilePath())) {
            copy($envFile, $backup);
        }

        $this->cleanEnvFile($envFile);
    }

    protected function cleanEnvFile(string $path): void
    {
        $cleanUpKeys = config('nativephp.cleanup_env_keys', []);

        $contents = collect(file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            ->filter(function (string $line) use ($cleanUpKeys) {
                $key = str($line)->before('=');

                return ! $key->is($cleanUpKeys)
                    && ! $key->startsWith('#');
            })
            ->join("\n");

        file_put_contents($path, $contents);
    }

    protected function restoreWebEnv(): void
    {
        copy($this->getBackupEnvFilePath(), app()->environmentFilePath());
        unlink($this->getBackupEnvFilePath());
    }

    protected function getBackupEnvFilePath(): string
    {
        return base_path('.env.backup');
    }
}
