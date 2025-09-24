<?php

namespace Native\Electron\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array builderOptions()
 * @method static array environmentVariables()
 */
class Updater extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'nativephp.updater';
    }
}
