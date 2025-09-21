<?php

namespace Native\Laravel\Fakes;

use Closure;
use Illuminate\Support\Arr;
use Native\Laravel\Client\Client;
use Native\Laravel\Contracts\WindowManager as WindowManagerContract;
use Native\Laravel\Windows\Window;
use PHPUnit\Framework\Assert as PHPUnit;
use Webmozart\Assert\Assert;

class WindowManagerFake implements WindowManagerContract
{
    public array $opened = [];

    public array $closed = [];

    public array $hidden = [];

    public array $shown = [];

    public array $forcedWindowReturnValues = [];

    public function __construct(
        protected Client $client
    ) {}

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

        $this->ensureForceReturnWindowsProvided();

        $matchingWindows = array_filter(
            $this->forcedWindowReturnValues,
            fn (Window $window) => $window->getId() === $id
        );

        if (empty($matchingWindows)) {
            return $this->forcedWindowReturnValues[array_rand($this->forcedWindowReturnValues)]->setClient($this->client);
        }

        Assert::count($matchingWindows, 1);

        return Arr::first($matchingWindows)->setClient($this->client);
    }

    public function close($id = null)
    {
        $this->closed[] = $id;
    }

    public function hide($id = null)
    {
        $this->hidden[] = $id;
    }

    public function show($id = null)
    {
        $this->shown[] = $id;
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

        Assert::notEmpty($matchingWindows);
        Assert::count($matchingWindows, 1);

        return Arr::first($matchingWindows);
    }

    /**
     * @param  string|Closure(string): bool  $id
     */
    public function assertOpened(string|Closure $id): void
    {
        if (is_callable($id) === false) {
            PHPUnit::assertContains($id, $this->opened);

            return;
        }

        $hit = empty(
            array_filter(
                $this->opened,
                fn (string $openedId) => $id($openedId) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  string|Closure(string): bool  $id
     */
    public function assertClosed(string|Closure $id): void
    {
        if (is_callable($id) === false) {
            PHPUnit::assertContains($id, $this->closed);

            return;
        }

        $hit = empty(
            array_filter(
                $this->closed,
                fn (mixed $closedId) => $id($closedId) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  string|Closure(string): bool  $id
     */
    public function assertHidden(string|Closure $id): void
    {
        if (is_callable($id) === false) {
            PHPUnit::assertContains($id, $this->hidden);

            return;
        }

        $hit = empty(
            array_filter(
                $this->hidden,
                fn (mixed $hiddenId) => $id($hiddenId) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  string|Closure(string): bool  $id
     */
    public function assertShown(string|Closure $id): void
    {
        if (is_callable($id) === false) {
            PHPUnit::assertContains($id, $this->shown);

            return;
        }

        $hit = empty(
            array_filter(
                $this->shown,
                fn (mixed $shownId) => $id($shownId) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    public function assertOpenedCount(int $expected): void
    {
        PHPUnit::assertCount($expected, $this->opened);
    }

    public function assertClosedCount(int $expected): void
    {
        PHPUnit::assertCount($expected, $this->closed);
    }

    public function assertHiddenCount(int $expected): void
    {
        PHPUnit::assertCount($expected, $this->hidden);
    }

    public function assertShownCount(int $expected): void
    {
        PHPUnit::assertCount($expected, $this->shown);
    }

    private function ensureForceReturnWindowsProvided(): void
    {
        Assert::notEmpty($this->forcedWindowReturnValues, 'No windows were provided to return');
    }
}
