<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Native\Laravel\GlobalShortcut key(string $key)
 * @method static \Native\Laravel\GlobalShortcut event(string $event)
 * @method static void register()
 * @method static void unregister()
 */
class GlobalShortcut extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\GlobalShortcut::class;
    }
}
