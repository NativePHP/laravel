<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void set(string $key, $value)
 * @method static mixed get(string $key, $default = null)
 * @method static bool has(string $key)
 * @method static void forget(string $key)
 * @method static void clear()
 *
 * @see \Native\Laravel\Settings
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Native\Laravel\Settings::class;
    }
}
