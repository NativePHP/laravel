<?php

namespace Native\Electron\Updater;

use Native\Electron\Updater\Contracts\Updater;

class GitHubProvider implements Updater
{
    public function __construct(protected array $config)
    {
    }

    public function environmentVariables(): array
    {
        return [
            'GH_TOKEN' => $this->config['token'],
        ];
    }

    public function builderOptions(): array
    {
        return [
            'provider' => 'github',
            'repo' => $this->config['repo'],
            'owner' => $this->config['owner'],
            'vPrefixedTagName' => $this->config['vPrefixedTagName'],
            'host' => $this->config['host'],
            'protocol' => $this->config['protocol'],
            'private' => $this->config['private'],
            'channel' => $this->config['channel'],
            'releaseType' => $this->config['releaseType'],
        ];
    }
}
