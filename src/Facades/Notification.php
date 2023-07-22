<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static object title(string $title)
 * @method static object event(string $event)
 * @method static object message(string $body)
 * @method static void show()
 */
class Notification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Notification::class;
    }
}
