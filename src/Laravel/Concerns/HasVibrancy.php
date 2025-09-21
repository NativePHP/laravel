<?php

namespace Native\Laravel\Concerns;

trait HasVibrancy
{
    protected string $vibrancy = 'appearance-based';

    protected string $backgroundColor = '#FFFFFF';

    protected bool $transparent = false;

    public function backgroundColor($color): self
    {
        $this->backgroundColor = $color;

        return $this;
    }

    public function transparent($value = true): self
    {
        $this->transparent = $value;
        if ($value === true) {
            $this->backgroundColor = '#00000000';
        }

        return $this;
    }

    public function vibrancy(string $vibrancy): self
    {
        $this->vibrancy = $vibrancy;

        return $this;
    }

    public function lightVibrancy(): self
    {
        return $this->vibrancy('light');
    }

    public function blendBackgroundBehindWindow(): self
    {
        $this->transparent();

        return $this->vibrancy('under-window');
    }

    public function darkVibrancy(): self
    {
        return $this->vibrancy('dark');
    }
}
