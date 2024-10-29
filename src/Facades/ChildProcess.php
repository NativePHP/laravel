<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Native\Laravel\ChildProcess[] all()
 * @method static \Native\Laravel\ChildProcess get(string $alias = null)
 * @method static \Native\Laravel\ChildProcess message(string $message, string $alias = null)
 * @method static \Native\Laravel\ChildProcess restart(string $alias = null)
 * @method static \Native\Laravel\ChildProcess start(string|array $cmd, string $alias, string $cwd = null, array $env = null, bool $persistent = false)
 * @method static \Native\Laravel\ChildProcess php(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static \Native\Laravel\ChildProcess artisan(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static \Native\Laravel\ChildProcess void stop(string $alias = null)
 */
class ChildProcess extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\ChildProcess::class;
    }
}
