<?php

namespace Native\Desktop\Facades;

use Illuminate\Support\Facades\Facade;
use Native\Desktop\Contracts\PowerMonitor as PowerMonitorContract;
use Native\Desktop\Fakes\PowerMonitorFake;

/**
 * @method static \Native\Desktop\Enums\SystemIdleStatesEnum getSystemIdleState(int $threshold)
 * @method static int getSystemIdleTime()
 * @method static \Native\Desktop\Enums\ThermalStatesEnum getCurrentThermalState()
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
