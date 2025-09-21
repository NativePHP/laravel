<?php

namespace Native\Laravel\Fakes;

use Closure;
use Native\Laravel\Contracts\GlobalShortcut as GlobalShortcutContract;
use PHPUnit\Framework\Assert as PHPUnit;

class GlobalShortcutFake implements GlobalShortcutContract
{
    /**
     * @var array<int, string>
     */
    public array $keys = [];

    /**
     * @var array<int, string>
     */
    public array $events = [];

    public int $registeredCount = 0;

    public int $unregisteredCount = 0;

    public function key(string $key): self
    {
        $this->keys[] = $key;

        return $this;
    }

    public function event(string $event): self
    {
        $this->events[] = $event;

        return $this;
    }

    public function register(): void
    {
        $this->registeredCount++;
    }

    public function unregister(): void
    {
        $this->unregisteredCount++;
    }

    /**
     * @param  string|Closure(string): bool  $key
     */
    public function assertKey(string|Closure $key): void
    {
        if (is_callable($key) === false) {
            PHPUnit::assertContains($key, $this->keys);

            return;
        }

        $hit = empty(
            array_filter(
                $this->keys,
                fn (string $keyIteration) => $key($keyIteration) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    /**
     * @param  string|Closure(string): bool  $event
     */
    public function assertEvent(string|Closure $event): void
    {
        if (is_callable($event) === false) {
            PHPUnit::assertContains($event, $this->events);

            return;
        }

        $hit = empty(
            array_filter(
                $this->events,
                fn (string $eventIteration) => $event($eventIteration) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    public function assertRegisteredCount(int $count): void
    {
        PHPUnit::assertSame($count, $this->registeredCount);
    }

    public function assertUnregisteredCount(int $count): void
    {
        PHPUnit::assertSame($count, $this->unregisteredCount);
    }
}
