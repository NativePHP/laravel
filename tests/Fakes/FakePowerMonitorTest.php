<?php

use Native\Laravel\Contracts\PowerMonitor as PowerMonitorContract;
use Native\Laravel\Facades\PowerMonitor;
use Native\Laravel\Fakes\PowerMonitorFake;
use PHPUnit\Framework\AssertionFailedError;

use function Pest\Laravel\swap;

it('swaps implementations using facade', function () {
    PowerMonitor::fake();

    expect(app(PowerMonitorContract::class))
        ->toBeInstanceOf(PowerMonitorFake::class);
});

it('asserts getSystemIdleState using int', function () {
    swap(PowerMonitorContract::class, $fake = app(PowerMonitorFake::class));

    $fake->getSystemIdleState(10);
    $fake->getSystemIdleState(60);

    $fake->assertGetSystemIdleState(10);
    $fake->assertGetSystemIdleState(60);

    try {
        $fake->assertGetSystemIdleState(20);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts getSystemIdleState using callable', function () {
    swap(PowerMonitorContract::class, $fake = app(PowerMonitorFake::class));

    $fake->getSystemIdleState(10);
    $fake->getSystemIdleState(60);

    $fake->assertGetSystemIdleState(fn (int $key) => $key === 10);
    $fake->assertGetSystemIdleState(fn (int $key) => $key === 60);

    try {
        $fake->assertGetSystemIdleState(fn (int $key) => $key === 20);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts getSystemIdleState count', function () {
    swap(PowerMonitorContract::class, $fake = app(PowerMonitorFake::class));

    $fake->getSystemIdleState(10);
    $fake->getSystemIdleState(20);
    $fake->getSystemIdleState(60);

    $fake->assertGetSystemIdleStateCount(3);

    try {
        $fake->assertGetSystemIdleStateCount(2);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts getSystemIdleTime count', function () {
    swap(PowerMonitorContract::class, $fake = app(PowerMonitorFake::class));

    $fake->getSystemIdleTime();
    $fake->getSystemIdleTime();
    $fake->getSystemIdleTime();

    $fake->assertGetSystemIdleTimeCount(3);

    try {
        $fake->assertGetSystemIdleTimeCount(2);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts getCurrentThermalState count', function () {
    swap(PowerMonitorContract::class, $fake = app(PowerMonitorFake::class));

    $fake->getCurrentThermalState();
    $fake->getCurrentThermalState();
    $fake->getCurrentThermalState();

    $fake->assertGetCurrentThermalStateCount(3);

    try {
        $fake->assertGetCurrentThermalStateCount(2);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});

it('asserts isOnBatteryPower count', function () {
    swap(PowerMonitorContract::class, $fake = app(PowerMonitorFake::class));

    $fake->isOnBatteryPower();
    $fake->isOnBatteryPower();
    $fake->isOnBatteryPower();

    $fake->assertIsOnBatteryPowerCount(3);

    try {
        $fake->assertIsOnBatteryPowerCount(2);
    } catch (AssertionFailedError) {
        return;
    }

    $this->fail('Expected assertion to fail');
});
