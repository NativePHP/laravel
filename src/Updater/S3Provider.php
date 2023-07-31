<?php

namespace Native\Electron\Updater;

use Native\Electron\Updater\Contracts\Updater;

class S3Provider implements Updater
{
    public function __construct(protected array $config)
    {
    }

    public function environmentVariables(): array
    {
        return [
            'AWS_PROFILE' => $this->config['profile'],
        ];
    }

    public function builderOptions(): array
    {
        return [
            'provider' => 's3',
            'endpoint' => $this->config['endpoint'],
            'region' => $this->config['region'],
            'bucket' => $this->config['bucket'],
            'path' => $this->config['path'],
            'acl' => $this->config['acl']
        ];
    }
}
