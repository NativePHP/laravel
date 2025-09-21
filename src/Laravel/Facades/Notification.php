<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static title(string $title)
 * @method static static event(string $event)
 * @method static static message(string $body)
 * @method static static reference(string $reference)
 * @method static static hasReply(string $placeholder = '')
 * @method static static addAction(string $label)
 * @method static void show()
 */
class Notification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Notification::class;
    }
}
