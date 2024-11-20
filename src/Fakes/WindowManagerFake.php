<?php

namespace Native\Laravel\Fakes;

use Illuminate\Support\Arr;
use Native\Laravel\Contracts\WindowManager as WindowManagerContract;
use Native\Laravel\Windows\Window;
use PHPUnit\Framework\Assert as PHPUnit;

class WindowManagerFake implements WindowManagerContract
{
    public array $opened = [];

    public array $closed = [];

    public array $hidden = [];

    public array $forcedWindowReturnValues = [];

    /**
     * @param  array<int, Window>  $windows
     */
    public function alwaysReturnWindows(array $windows): self
    {
        $this->forcedWindowReturnValues = $windows;

        return $this;
    }

    public function open(string $id = 'main')
    {
        $this->opened[] = $id;
    }

    public function close($id = null)
    {
        $this->closed[] = $id;
    }

    public function hide($id = null)
    {
        $this->hidden[] = $id;
    }

    public function current(): Window
    {
        $this->ensureForceReturnWindowsProvided();

        return $this->forcedWindowReturnValues[array_rand($this->forcedWindowReturnValues)];
    }

    /**
     * @return array<int, Window>
     */
    public function all(): array
    {
        $this->ensureForceReturnWindowsProvided();

        return $this->forcedWindowReturnValues;
    }

    public function get(string $id): Window
    {
        $this->ensureForceReturnWindowsProvided();

        $matchingWindows = array_filter($this->forcedWindowReturnValues, fn (Window $window) => $window->getId() === $id);

        PHPUnit::assertNotEmpty($matchingWindows);
        PHPUnit::assertCount(1, $matchingWindows);

        return Arr::first($matchingWindows);
    }

    public function assertOpened(string $id): void
    {
        PHPUnit::assertContains($id, $this->opened);
    }

    public function assertClosed(?string $id): void
    {
        PHPUnit::assertContains($id, $this->closed);
    }

    public function assertHidden(?string $id): void
    {
        PHPUnit::assertContains($id, $this->hidden);
    }

    private function ensureForceReturnWindowsProvided(): void
    {
        PHPUnit::assertNotEmpty($this->forcedWindowReturnValues);
    }
}
