<?php

namespace Native\Desktop\Contracts;

use Native\Desktop\Enums\SystemIdleStatesEnum;
use Native\Desktop\Enums\ThermalStatesEnum;

interface PowerMonitor
{
    public function getSystemIdleState(int $threshold): SystemIdleStatesEnum;

    public function getSystemIdleTime(): int;

    public function getCurrentThermalState(): ThermalStatesEnum;

    public function isOnBatteryPower(): bool;
}
