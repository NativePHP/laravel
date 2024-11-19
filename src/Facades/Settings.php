<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void set($key, $value)
 * @method static mixed get($key, $default = null)
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Settings::class;
    }
}
