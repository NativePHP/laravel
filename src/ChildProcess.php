<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class ChildProcess
{
    public function __construct(protected Client $client) {}

    public function start(string|array $cmd, string $alias, ?string $cwd = null, ?array $env = null): self
    {
        $cwd = $cwd ?? base_path();

        if (is_string($cmd)) {
            // When a string is passed, explode it on the space
            $cmd = array_values(array_filter(explode(' ', $cmd)));
        }

        $this->client->post('child-process/start', [
            'alias' => $alias,
            'cmd' => $cmd,
            'cwd' => $cwd,
            'env' => $env,
        ])->json();

        return $this;
    }

    public function artisan(string|array $cmd, string $alias, ?array $env = null): self
    {
        $cmd = [PHP_BINARY, 'artisan', ...(array) $cmd];

        return $this->start($cmd, $alias, env: $env);
    }

    public function stop(string $alias): void
    {
        $this->client->post('child-process/stop', [
            'alias' => $alias,
        ])->json();
    }

    public function message(mixed $message, string $alias): void
    {
        $this->client->post('child-process/message', [
            'message' => json_encode($message),
            'alias' => $alias,
        ])->json();
    }
}
