<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class ChildProcess
{
    private string $alias;

    private ?array $process;

    public function __construct(protected Client $client) {}

    public function start(string $alias, string|array $cmd, ?string $cwd = null, ?array $env = null): object
    {
        $this->alias = $alias;

        $cwd = $cwd ?? base_path();

        $cmd = is_iterable($cmd)
            // when an array is passed, escape spaces for each item
            ? array_map(fn ($a) => str_replace(' ', '\ ', $a), $cmd)
            // when a string is passed, explode it on the space
            : array_values(array_filter(explode(' ', $cmd)));

        $this->process = $this->client->post('child-process/start', [
            'alias' => $alias,
            'cmd' => $cmd,
            'cwd' => $cwd,
            'env' => $env,
        ])->json();

        return $this;
    }

    public function artisan(string $alias, string|array $cmd, ?array $env = null): object
    {
        $cmd = [PHP_BINARY, 'artisan', ...(array) $cmd];

        return $this->start($alias, $cmd, env: $env);
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
