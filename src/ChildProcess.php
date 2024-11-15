<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class ChildProcess
{
    public readonly int $pid;

    public readonly string $alias;

    public readonly array $cmd;

    public readonly ?string $cwd;

    public readonly ?array $env;

    public readonly bool $persistent;

    public function __construct(protected Client $client) {}

    public function get(?string $alias = null): ?static
    {
        $alias = $alias ?? $this->alias;

        $process = $this->client->get("child-process/get/{$alias}")->json();

        if (! $process) {
            return null;
        }

        return $this->fromRuntimeProcess($process);
    }

    public function all(): array
    {
        $processes = $this->client->get('child-process/')->json();

        if (empty($processes)) {
            return [];
        }

        $hydrated = [];

        foreach ($processes as $alias => $process) {
            $hydrated[$alias] = (new static($this->client))
                ->fromRuntimeProcess($process);
        }

        return $hydrated;
    }

    public function start(
        string|array $cmd,
        string $alias,
        ?string $cwd = null,
        ?array $env = null,
        bool $persistent = false
    ): static {

        $process = $this->client->post('child-process/start', [
            'alias' => $alias,
            'cmd' => (array) $cmd,
            'cwd' => $cwd ?? base_path(),
            'env' => $env,
            'persistent' => $persistent,
        ])->json();

        return $this->fromRuntimeProcess($process);
    }

    public function php(string|array $cmd, string $alias, ?array $env = null, ?bool $persistent = false): self
    {
        $process = $this->client->post('child-process/start-php', [
            'alias' => $alias,
            'cmd' => (array) $cmd,
            'cwd' => $cwd ?? base_path(),
            'env' => $env,
            'persistent' => $persistent,
        ])->json();

        return $this->fromRuntimeProcess($process);
    }

    public function artisan(string|array $cmd, string $alias, ?array $env = null, ?bool $persistent = false): self
    {
        $cmd = ['artisan', ...(array) $cmd];

        return $this->php($cmd, $alias, env: $env, persistent: $persistent);
    }

    public function stop(?string $alias = null): void
    {
        $this->client->post('child-process/stop', [
            'alias' => $alias ?? $this->alias,
        ])->json();
    }

    public function restart(?string $alias = null): ?static
    {
        $process = $this->client->post('child-process/restart', [
            'alias' => $alias ?? $this->alias,
        ])->json();

        if (! $process) {
            return null;
        }

        return $this->fromRuntimeProcess($process);
    }

    public function message(string $message, ?string $alias = null): static
    {
        $this->client->post('child-process/message', [
            'alias' => $alias ?? $this->alias,
            'message' => $message,
        ])->json();

        return $this;
    }

    protected function fromRuntimeProcess($process): static
    {
        if (isset($process['pid'])) {
            $this->pid = $process['pid'];
        }

        foreach ($process['settings'] as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }
}
