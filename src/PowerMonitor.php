<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Native\Laravel\Enums\SystemIdleStatesEnum;
use Native\Laravel\Enums\ThermalStatesEnum;

class PowerMonitor
{
    public function __construct(protected Client $client) {}

    public function getSystemIdleState(int $threshold): SystemIdleStatesEnum
    {
        $result = $this->client->get('power-monitor/get-system-idle-state', [
            'threshold' => $threshold,
        ])->json('result');

        return SystemIdleStatesEnum::tryFrom($result) ?? SystemIdleStatesEnum::UNKNOWN;
    }

    public function getSystemIdleTime(): int
    {
        return $this->client->get('power-monitor/get-system-idle-time')->json('result');
    }

    public function getCurrentThermalState(): ThermalStatesEnum
    {
        $result = $this->client->get('power-monitor/get-current-thermal-state')->json('result');

        return ThermalStatesEnum::tryFrom($result) ?? ThermalStatesEnum::UNKNOWN;
    }

    public function isOnBatteryPower(): bool
    {
        return $this->client->get('power-monitor/is-on-battery-power')->json('result');
    }
}
