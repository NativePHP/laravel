<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Native\Laravel\ChildProcess[] all()
 * @method static \Native\Laravel\ChildProcess get(string $alias)
 * @method static \Native\Laravel\ChildProcess message(string $message, string $alias = null)
 * @method static \Native\Laravel\ChildProcess restart(string $alias)
 * @method static \Native\Laravel\ChildProcess start(string $alias, array $cmd, string $cwd = null, array $env = null, bool $persistent = false)
 * @method static void stop(string $alias)
 */
class ChildProcess extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\ChildProcess::class;
    }
}
