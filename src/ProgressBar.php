<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class ProgressBar
{
    /**
     * The current progress percentage.
     *
     * @var float
     */
    protected float $percent = 0;

    /**
     * The current step value.
     *
     * @var int
     */
    protected int $step = 0;

    /**
     * The timestamp of the last write operation.
     *
     * @var float
     */
    protected float $lastWriteTime = 0;

    /**
     * The minimum time interval (in seconds) between progress bar redraws.
     *
     * @var float
     */
    protected float $minSecondsBetweenRedraws = 0.1;

    /**
     * The maximum time interval (in seconds) between progress bar redraws.
     *
     * @var float
     */
    protected float $maxSecondsBetweenRedraws = 1;

    /**
     * Constructor.
     *
     * @param int $maxSteps The total number of steps for the progress bar.
     * @param Client $client The HTTP client instance to make API requests.
     */
    public function __construct(protected int $maxSteps, protected Client $client)
    {
    }

    /**
     * Create a new instance of the ProgressBar.
     *
     * @param int $maxSteps The total number of steps for the progress bar.
     * @return static A new instance of the ProgressBar class.
     */
    public static function create(int $maxSteps): static
    {
        return new static($maxSteps, new Client());
    }

    /**
     * Start the progress bar.
     *
     * @return void
     */
    public function start()
    {
        $this->lastWriteTime = microtime(true);
        $this->setProgress(0);
    }

    /**
     * Advance the progress bar by a specified number of steps.
     *
     * @param int $step The number of steps to advance the progress bar.
     * @return void
     */
    public function advance($step = 1)
    {
        $this->setProgress($this->step + $step);
    }

    /**
     * Set the current progress of the progress bar.
     *
     * @param int $step The current step value for the progress bar.
     * @return void
     */
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

    /**
     * Finish the progress bar.
     *
     * @return void
     */
    public function finish()
    {
        $this->client->post('progress-bar/update', [
            'percent' => -1,
        ]);
    }

    /**
     * Display the current progress of the progress bar.
     *
     * @return void
     */
    public function display()
    {
        $this->lastWriteTime = microtime(true);

        $this->client->post('progress-bar/update', [
            'percent' => $this->percent,
        ]);
    }
}
