<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Contracts\QueueWorker as QueueWorkerContract;
use Native\Laravel\DTOs\QueueConfig;
use Native\Laravel\Fakes\QueueWorkerFake;

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
