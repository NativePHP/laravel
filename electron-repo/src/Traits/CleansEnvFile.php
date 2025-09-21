<?php

/**
 * This trait is responsible for cleaning any sensitive information from the .env file
 * and also injects some defaults that need to be set as soon as possible.
 *
 * TODO: When more drivers/adapters are added, this should be relocated
 */

namespace Native\Electron\Traits;

trait CleansEnvFile
{
    abstract protected function buildPath(string $path = ''): string;

    public array $overrideKeys = [
        'LOG_CHANNEL',
        'LOG_STACK',
        'LOG_DAILY_DAYS',
        'LOG_LEVEL',
    ];

    public function cleanEnvFile(): void
    {
        $cleanUpKeys = array_merge($this->overrideKeys, config('nativephp.cleanup_env_keys', []));

        $envFile = $this->buildPath(app()->environmentFile());

        $contents = collect(file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
            // Remove cleanup keys
            ->filter(function (string $line) use ($cleanUpKeys) {
                $key = str($line)->before('=');

                return ! $key->is($cleanUpKeys)
                    && ! $key->startsWith('#');
            })
            // Set defaults (other config overrides are handled in the NativeServiceProvider)
            // The Log channel needs to be configured before anything else.
            ->push('LOG_CHANNEL=stack')
            ->push('LOG_STACK=daily')
            ->push('LOG_DAILY_DAYS=3')
            ->push('LOG_LEVEL=warning')
            ->join("\n");

        file_put_contents($envFile, $contents);
    }
}
