<?php

namespace Native\Desktop\Fakes;

use Closure;
use Native\Desktop\Contracts\QueueWorker as QueueWorkerContract;
use Native\Desktop\DTOs\QueueConfig;
use PHPUnit\Framework\Assert as PHPUnit;

class QueueWorkerFake implements QueueWorkerContract
{
    /**
     * @var array<int, QueueConfig>
     */
    public array $ups = [];

    /**
     * @var array<int, string>
     */
    public array $downs = [];

    public function up(QueueConfig $config): void
    {
        $this->ups[] = $config;
    }

    public function down(string $alias): void
    {
        $this->downs[] = $alias;
    }

    public function assertUp(Closure $callback): void
    {
        $hit = empty(
            array_filter(
                $this->ups,
                fn (QueueConfig $up) => $callback($up) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }

    public function assertDown(string|Closure $alias): void
    {
        if (is_callable($alias) === false) {
            PHPUnit::assertContains($alias, $this->downs);

            return;
        }

        $hit = empty(
            array_filter(
                $this->downs,
                fn (string $down) => $alias($down) === true
            )
        ) === false;

        PHPUnit::assertTrue($hit);
    }
}
