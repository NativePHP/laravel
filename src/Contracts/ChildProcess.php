<?php

namespace Native\Laravel\Contracts;

interface ChildProcess
{
    public function get(?string $alias = null): ?self;

    public function all(): array;

    public function start(
        string|array $cmd,
        string $alias,
        ?string $cwd = null,
        ?array $env = null,
        bool $persistent = false
    ): self;

    public function php(string|array $cmd, string $alias, ?array $env = null, ?bool $persistent = false, ?array $iniSettings = null): self;

    public function artisan(string|array $cmd, string $alias, ?array $env = null, ?bool $persistent = false, ?array $iniSettings = null): self;

    public function stop(?string $alias = null): void;

    public function restart(?string $alias = null): ?self;

    public function message(string $message, ?string $alias = null): self;
}
