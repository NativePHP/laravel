<?php

namespace Native\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Native\Laravel\Enums\SystemIdelStatesEnum getSystemIdleState(int $threshold)
 * @method static int getSystemIdleTime()
 * @method static \Native\Laravel\Enums\ThermalStatesEnum getCurrentThermalState()
 * @method static bool isOnBatteryPower()
 */
class PowerMonitor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Native\Laravel\PowerMonitor::class;
    }
}
