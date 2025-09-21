<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Contracts\GlobalShortcut as GlobalShortcutContract;
use Native\Laravel\Fakes\GlobalShortcutFake;

/**
 * @method static \Native\Laravel\GlobalShortcut key(string $key)
 * @method static \Native\Laravel\GlobalShortcut event(string $event)
 * @method static void register()
 * @method static void unregister()
 */
class GlobalShortcut extends Facade
{
    public static function fake()
    {
        return tap(static::getFacadeApplication()->make(GlobalShortcutFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor()
    {
        return GlobalShortcutContract::class;
    }
}
