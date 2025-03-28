<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static static new()
 * @method static static type(string $type)
 * @method static static title(string $title)
 * @method static static detail(string $detail)
 * @method static static buttons(string[] $buttons)
 * @method static static defaultId(int $defaultId)
 * @method static static cancelId(int $cancelId)
 * @method static int show(string $message)
 * @method static bool error(string $title, string $message)
 */
class Alert extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\Alert::class;
    }
}
