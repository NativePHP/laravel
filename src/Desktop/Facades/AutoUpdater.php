<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void checkForUpdates()
 * @method static void quitAndInstall()
 * @method static void downloadUpdate()
 */
class AutoUpdater extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Desktop\AutoUpdater::class;
    }
}
