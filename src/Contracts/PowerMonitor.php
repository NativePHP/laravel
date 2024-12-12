<?php

namespace Native\Laravel\Contracts;

use Native\Laravel\Enums\SystemIdleStatesEnum;
use Native\Laravel\Enums\ThermalStatesEnum;

interface PowerMonitor
{
    public function getSystemIdleState(int $threshold): SystemIdleStatesEnum;

    public function getSystemIdleTime(): int;

    public function getCurrentThermalState(): ThermalStatesEnum;

    public function isOnBatteryPower(): bool;
}
