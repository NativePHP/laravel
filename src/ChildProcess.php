<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class ChildProcess
{
    private string $alias;

    private ?array $process;

    public function __construct(protected Client $client) {}

    public function start(string $alias, array $cmd, ?string $cwd = null, ?array $env = null): object
    {
        $this->alias = $alias;

        $this->process = $this->client->post('child-process/start', [
            'alias' => $alias,
            'cmd' => $cmd,
            'cwd' => base_path(),
            'env' => $env,
        ])->json();

        return $this;
    }

    public function stop(string $alias): void
    {
        $this->client->post('child-process/stop', [
            'alias' => $alias,
        ])->json();
    }

    public function message(string $alias, mixed $message): void
    {
        $this->client->post('child-process/message', [
            'alias' => $alias,
            'message' => json_encode($message),
        ])->json();
    }
}
