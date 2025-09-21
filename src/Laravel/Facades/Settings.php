<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void set(string $key, $value)
 * @method static mixed get(string $key, $default = null)
 * @method static void forget(string $key)
 * @method static void clear()
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Settings::class;
    }
}
