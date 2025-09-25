<?php

namespace Native\Desktop;

use Native\Desktop\Client\Client;
use Native\Desktop\Contracts\PowerMonitor as PowerMonitorContract;
use Native\Desktop\Enums\SystemIdleStatesEnum;
use Native\Desktop\Enums\ThermalStatesEnum;

class PowerMonitor implements PowerMonitorContract
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
