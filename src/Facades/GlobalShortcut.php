<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Contracts\GlobalShortcut as GlobalShortcutContract;
use Native\Desktop\Fakes\GlobalShortcutFake;

/**
 * @method static \Native\Desktop\GlobalShortcut key(string $key)
 * @method static \Native\Desktop\GlobalShortcut event(string $event)
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
