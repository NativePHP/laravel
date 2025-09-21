<?php

namespace Native\Laravel\Fakes;

use Closure;
use Native\Laravel\Contracts\ChildProcess as ChildProcessContract;
use PHPUnit\Framework\Assert as PHPUnit;

class ChildProcessFake implements ChildProcessContract
{
    /**
     * @var array<int, string|null>
     */
    public array $gets = [];

    /**
     * @var array<int, array{cmd: array|string, alias: string, cwd: string|null, env: array|null, persistent: bool}>
     */
    public array $starts = [];

    /**
     * @var array<int, array{cmd: array|string, alias: string, env: array|null, persistent: bool}>
     */
    public array $phps = [];

    /**
     * @var array<int, array{cmd: array|string, alias: string, env: array|null, persistent: bool}>
     */
    public array $artisans = [];

    /**
     * @var array<int, string|null>
     */
    public array $stops = [];

    /**
     * @var array<int, string|null>
     */
    public array $restarts = [];

    /**
     * @var array<int, array{message: string, alias: string|null}>
     */
    public array $messages = [];

    public function get(?string $alias = null): self
    {
        $this->gets[] = $alias;

        return $this;
    }

    public function all(): array
    {
        return [$this];
    }

    public function start(
        array|string $cmd,
        string $alias,
        ?string $cwd = null,
        ?array $env = null,
        bool $persistent = false
    ): self {
        $this->starts[] = [
            'cmd' => $cmd,
            'alias' => $alias,
            'cwd' => $cwd,
            'env' => $env,
            'persistent' => $persistent,
        ];

        return $this;
    }

    public function php(
        array|string $cmd,
        string $alias,
        ?array $env = null,
        ?bool $persistent = false,
        ?array $iniSettings = null
    ): self {
        $this->phps[] = [
            'cmd' => $cmd,
            'alias' => $alias,
            'env' => $env,
            'persistent' => $persistent,
            'iniSettings' => $iniSettings,
        ];

        return $this;
    }

    public function artisan(
        array|string $cmd,
        string $alias,
        ?array $env = null,
        ?bool $persistent = false,
        ?array $iniSettings = null
    ): self {
        $this->artisans[] = [
            'cmd' => $cmd,
            'alias' => $alias,
            'env' => $env,
            'persistent' => $persistent,
            'iniSettings' => $iniSettings,
        ];

        return $this;
    }

    public function stop(?string $alias = null): void
    {
        $this->stops[] = $alias;
    }

    public function restart(?string $alias = null): self
    {
        $this->restarts[] = $alias;

        return $this;
    }

    public function message(string $message, ?string $alias = null): self
    {
        $this->messages[] = [
            'message' => $message,
            'alias' => $alias,
        ];

        return $this;
    }

    /**
     * @param  string|Closure(string): bool  $alias
     */
    public function assertGet(string|Closure $alias): void
    {
        if (is_callable($alias) === false) {
            PHPUnit::assertContains($alias, $this->gets);

            return;
        }

        $hit = empty(
            array_filter(
                $this->gets,
                fn (mixed $get) => $alias($get) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  Closure(array|string $cmd, string $alias, ?string $cwd, ?array $env, bool $persistent): bool  $callback
     */
    public function assertStarted(Closure $callback): void
    {
        $hit = empty(
            array_filter(
                $this->starts,
                fn (array $started) => $callback(...$started) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  Closure(array|string $cmd, string $alias, ?array $env, ?bool $persistent): bool  $callback
     */
    public function assertPhp(Closure $callback): void
    {
        $hit = empty(
            array_filter(
                $this->phps,
                fn (array $php) => $callback(...$php) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  Closure(array|string $cmd, string $alias, ?array $env, ?bool $persistent): bool  $callback
     */
    public function assertArtisan(Closure $callback): void
    {
        $hit = empty(
            array_filter(
                $this->artisans,
                fn (array $artisan) => $callback(...$artisan) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  string|Closure(string): bool  $alias
     */
    public function assertStop(string|Closure $alias): void
    {
        if (is_callable($alias) === false) {
            PHPUnit::assertContains($alias, $this->stops);

            return;
        }

        $hit = empty(
            array_filter(
                $this->stops,
                fn (mixed $stop) => $alias($stop) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  string|Closure(string): bool  $alias
     */
    public function assertRestart(string|Closure $alias): void
    {
        if (is_callable($alias) === false) {
            PHPUnit::assertContains($alias, $this->restarts);

            return;
        }

        $hit = empty(
            array_filter(
                $this->restarts,
                fn (mixed $restart) => $alias($restart) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  Closure(string $message, string|null $alias): bool  $callback
     */
    public function assertMessage(Closure $callback): void
    {
        $hit = empty(
            array_filter(
                $this->messages,
                fn (array $message) => $callback(...$message) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }
}
