<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Contracts\ChildProcess as ChildProcessContract;
use Native\Desktop\Fakes\ChildProcessFake;

/**
 * @method static \Native\Desktop\ChildProcess[] all()
 * @method static \Native\Desktop\ChildProcess|null get(string $alias = null)
 * @method static \Native\Desktop\ChildProcess message(string $message, string $alias = null)
 * @method static \Native\Desktop\ChildProcess restart(string $alias = null)
 * @method static \Native\Desktop\ChildProcess start(string|array $cmd, string $alias, string $cwd = null, array $env = null, bool $persistent = false)
 * @method static \Native\Desktop\ChildProcess php(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
 * @method static \Native\Desktop\ChildProcess artisan(string|array $cmd, string $alias, array $env = null, bool $persistent = false)
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
