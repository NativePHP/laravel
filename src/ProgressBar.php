<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class ProgressBar
{
    protected float $percent = 0;

    protected int $step = 0;

    protected float $lastWriteTime = 0;

    protected float $minSecondsBetweenRedraws = 0.1;

    protected float $maxSecondsBetweenRedraws = 1;

    public function __construct(protected int $maxSteps, protected Client $client) {}

    public static function create(int $maxSteps): static
    {
        return new static($maxSteps, new Client);
    }

    public function start()
    {
        $this->lastWriteTime = microtime(true);
        $this->setProgress(0);
    }

    public function advance($step = 1)
    {
        $this->setProgress($this->step + $step);
    }

    public function setProgress(int $step)
    {
        if ($this->maxSteps && $step > $this->maxSteps) {
            $this->maxSteps = $step;
        } elseif ($step < 0) {
            $step = 0;
        }

        $redrawFreq = 1;
        $prevPeriod = (int) ($this->step / $redrawFreq);
        $currPeriod = (int) ($step / $redrawFreq);

        $this->step = $step;
        $this->percent = $this->maxSteps ? (float) $this->step / $this->maxSteps : 0;
        $timeInterval = microtime(true) - $this->lastWriteTime;

        // Draw regardless of other limits
        if ($this->maxSteps === $step) {
            $this->display();

            return;
        }

        // Throttling
        if ($timeInterval < $this->minSecondsBetweenRedraws) {
            return;
        }

        // Draw each step period, but not too late
        if ($prevPeriod !== $currPeriod || $timeInterval >= $this->maxSecondsBetweenRedraws) {
            $this->display();
        }
    }

    public function finish()
    {
        $this->client->post('progress-bar/update', [
            'percent' => -1,
        ]);
    }

    public function display()
    {
        $this->lastWriteTime = microtime(true);

        $this->client->post('progress-bar/update', [
            'percent' => $this->percent,
        ]);
    }
}
