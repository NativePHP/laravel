<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Native\Laravel\Contracts\ChildProcess as ChildProcessContract;

class ChildProcess implements ChildProcessContract
{
    public readonly int $pid;

    public readonly string $alias;

    public readonly array $cmd;

    public readonly ?string $cwd;

    public readonly ?array $env;

    public readonly bool $persistent;

    public readonly ?array $iniSettings;

    final public function __construct(protected Client $client) {}

    public function get(?string $alias = null): ?self
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

    /**
     * @param  string|string[]  $cmd
     * @return $this
     */
    public function start(
        string|array $cmd,
        string $alias,
        ?string $cwd = null,
        ?array $env = null,
        bool $persistent = false
    ): self {
        $cmd = is_array($cmd) ? array_values($cmd) : [$cmd];

        $process = $this->client->post('child-process/start', [
            'alias' => $alias,
            'cmd' => $cmd,
            'cwd' => $cwd ?? base_path(),
            'env' => $env,
            'persistent' => $persistent,
        ])->json();

        return $this->fromRuntimeProcess($process);
    }

    /**
     * @param  string|string[]  $cmd
     * @return $this
     */
    public function php(string|array $cmd, string $alias, ?array $env = null, ?bool $persistent = false, ?array $iniSettings = null): self
    {
        $cmd = is_array($cmd) ? array_values($cmd) : [$cmd];

        $process = $this->client->post('child-process/start-php', [
            'alias' => $alias,
            'cmd' => $cmd,
            'cwd' => base_path(),
            'env' => $env,
            'persistent' => $persistent,
            'iniSettings' => $iniSettings,
        ])->json();

        return $this->fromRuntimeProcess($process);
    }

    /**
     * @param  string|string[]  $cmd
     * @return $this
     */
    public function artisan(string|array $cmd, string $alias, ?array $env = null, ?bool $persistent = false, ?array $iniSettings = null): self
    {
        $cmd = is_array($cmd) ? array_values($cmd) : [$cmd];

        $cmd = ['artisan', ...$cmd];

        return $this->php($cmd, $alias, env: $env, persistent: $persistent, iniSettings: $iniSettings);
    }

    public function stop(?string $alias = null): void
    {
        $this->client->post('child-process/stop', [
            'alias' => $alias ?? $this->alias,
        ])->json();
    }

    public function restart(?string $alias = null): ?self
    {
        $process = $this->client->post('child-process/restart', [
            'alias' => $alias ?? $this->alias,
        ])->json();

        if (! $process) {
            return null;
        }

        return $this->fromRuntimeProcess($process);
    }

    public function message(string $message, ?string $alias = null): self
    {
        $this->client->post('child-process/message', [
            'alias' => $alias ?? $this->alias,
            'message' => $message,
        ])->json();

        return $this;
    }

    protected function fromRuntimeProcess($process)
    {
        if (isset($process['pid'])) {
            // @phpstan-ignore-next-line
            $this->pid = $process['pid'];
        }

        foreach ($process['settings'] as $key => $value) {
            if (! property_exists($this, $key)) {
                throw new \RuntimeException("Property {$key} does not exist on ".__CLASS__);
            }

            $this->{$key} = $value;
        }

        return $this;
    }
}
