<?php

namespace Native\Laravel\Concerns;

use Closure;
use Native\Laravel\ProgressBar;

trait InteractsWithNativeApp
{
    public function withProgressBar($totalSteps, Closure $callback)
    {
        $bar = ProgressBar::create(
            is_iterable($totalSteps) ? count($totalSteps) : $totalSteps
        );

        $bar->start();

        if (is_iterable($totalSteps)) {
            foreach ($totalSteps as $value) {
                $callback($value, $bar);

                $bar->advance();
            }
        } else {
            $callback($bar);
        }

        $bar->finish();

        if (is_iterable($totalSteps)) {
            return $totalSteps;
        }
    }
}
