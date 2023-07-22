<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool canPromptTouchID()
 * @method static bool promptTouchID(string $reason)
 */
class System extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\System::class;
    }
}
