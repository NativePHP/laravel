<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string start(string $alias, array $cmd, string $cwd = null, array $env = null)
 * @method static string stop(string $alias)
 * @method static string message(string $alias, mixed $message)
 */
class ChildProcess extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\ChildProcess::class;
    }
}
