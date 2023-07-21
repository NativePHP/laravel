<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string arch()
 * @method static string platform()
 * @method static float uptime()
 * @method static object fresh()
 */
class Process extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Process::class;
    }
}
