<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Contracts\ChildProcess as ChildProcessContract;
use Native\Laravel\Fakes\ChildProcessFake;

/**
 * @method static \Native\Laravel\ChildProcess[] all()
 * @method static \Native\Laravel\ChildProcess|null get(string $alias = null)
 * @method static \Native\Laravel\ChildProcess message(string $message, string $alias = null)
 * @method static \Native\Laravel\ChildProcess restart(string $alias = null)
 * @method static \Native\Laravel\ChildProcess start(string|array $cmd, string $alias, string $cwd = null, array $env = null, bool $persistent = false)
 * @method static \Native\Laravel\ChildProcess php(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static \Native\Laravel\ChildProcess artisan(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static void stop(string $alias = null)
 */
class ChildProcess extends Facade
{
    public static function fake()
    {
        return tap(static::getFacadeApplication()->make(ChildProcessFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor()
    {
        self::clearResolvedInstance(ChildProcessContract::class);

        return ChildProcessContract::class;
    }
}
