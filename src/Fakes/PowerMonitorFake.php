<?php

namespace Native\Laravel\Fakes;

use Closure;
use Native\Laravel\Contracts\PowerMonitor as PowerMonitorContract;
use Native\Laravel\Enums\SystemIdleStatesEnum;
use Native\Laravel\Enums\ThermalStatesEnum;
use PHPUnit\Framework\Assert as PHPUnit;

class PowerMonitorFake implements PowerMonitorContract
{
    public array $getSystemIdleStateCalls = [];

    public int $getSystemIdleStateCount = 0;

    public int $getSystemIdleTimeCount = 0;

    public int $getCurrentThermalStateCount = 0;

    public int $isOnBatteryPowerCount = 0;

    public function getSystemIdleState(int $threshold): SystemIdleStatesEnum
    {
        $this->getSystemIdleStateCount++;

        $this->getSystemIdleStateCalls[] = $threshold;

        return SystemIdleStatesEnum::UNKNOWN;
    }

    public function getSystemIdleTime(): int
    {
        $this->getSystemIdleTimeCount++;

        return 0;
    }

    public function getCurrentThermalState(): ThermalStatesEnum
    {
        $this->getCurrentThermalStateCount++;

        return ThermalStatesEnum::UNKNOWN;
    }

    public function isOnBatteryPower(): bool
    {
        $this->isOnBatteryPowerCount++;

        return false;
    }

    /**
     * @param  int|Closure(int): bool  $key
     */
    public function assertGetSystemIdleState(int|Closure $key): void
    {
        if (is_callable($key) === false) {
            PHPUnit::assertContains($key, $this->getSystemIdleStateCalls);

            return;
        }

        $hit = empty(
            array_filter(
                $this->getSystemIdleStateCalls,
                fn (int $keyIteration) => $key($keyIteration) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    public function assertGetSystemIdleStateCount(int $count): void
    {
        PHPUnit::assertSame($count, $this->getSystemIdleStateCount);
    }

    public function assertGetSystemIdleTimeCount(int $count): void
    {
        PHPUnit::assertSame($count, $this->getSystemIdleTimeCount);
    }

    public function assertGetCurrentThermalStateCount(int $count): void
    {
        PHPUnit::assertSame($count, $this->getCurrentThermalStateCount);
    }

    public function assertIsOnBatteryPowerCount(int $count): void
    {
        PHPUnit::assertSame($count, $this->isOnBatteryPowerCount);
    }
}
