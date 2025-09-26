<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Contracts\QueueWorker as QueueWorkerContract;
use Native\Desktop\DataObjects\QueueConfig;
use Native\Desktop\Fakes\QueueWorkerFake;

/**
 * @method static void up(QueueConfig $config)
 * @method static void down(string $alias)
 */
class QueueWorker extends Facade
{
    public static function fake()
    {
        return tap(static::getFacadeApplication()->make(QueueWorkerFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor(): string
    {
        self::clearResolvedInstance(QueueWorkerContract::class);

        return QueueWorkerContract::class;
    }
}
