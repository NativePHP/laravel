<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Laravel\Contracts\PowerMonitor as PowerMonitorContract;
use Native\Laravel\Fakes\PowerMonitorFake;

/**
 * @method static \Native\Laravel\Enums\SystemIdleStatesEnum getSystemIdleState(int $threshold)
 * @method static int getSystemIdleTime()
 * @method static \Native\Laravel\Enums\ThermalStatesEnum getCurrentThermalState()
 * @method static bool isOnBatteryPower()
 */
class PowerMonitor extends Facade
{
    public static function fake()
    {
        return tap(static::getFacadeApplication()->make(PowerMonitorFake::class), function ($fake) {
            static::swap($fake);
        });
    }

    protected static function getFacadeAccessor(): string
    {
        return PowerMonitorContract::class;
    }
}
