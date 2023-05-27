<?php

namespace Native\Electron\Updater;

use Native\Electron\Updater\Contracts\Updater;

class S3Provider implements Updater
{
    public function __construct(protected array $config)
    {
    }

    public function providedEnvironmentVariables(): array
    {
        return [];
    }
}
