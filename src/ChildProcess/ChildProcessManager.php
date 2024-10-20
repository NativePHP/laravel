<?php

namespace Native\Laravel\ChildProcess;

use Native\Laravel\Client\Client;

class ChildProcessManager
{
    public function __construct(protected Client $client) {}

    public function start(string $alias, array $cmd, ?string $cwd = null, ?array $env = null): object
    {
        $this->process = $this->client->post('child-process/start')->json([
            'alias' => $alias,
            'cmd' => $cmd,
            'cwd' => $cwd,
            'env' => $env,
        ]);
    }

    public function onMessage() {}
}
