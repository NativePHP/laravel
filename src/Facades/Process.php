<?php

namespace Native\Desktop\Facades;

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
        return \Native\Desktop\Process::class;
    }
}
