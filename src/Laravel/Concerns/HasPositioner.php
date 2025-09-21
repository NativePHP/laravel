<?php

namespace Native\Laravel\Concerns;

trait HasPositioner
{
    protected string $windowPosition = 'trayCenter';

    public function windowPosition(string $position): self
    {
        $this->windowPosition = $position;

        return $this;
    }

    public function trayLeft(): self
    {
        return $this->windowPosition('trayLeft');
    }

    public function trayBottomLeft(): self
    {
        return $this->windowPosition('trayBottomLeft');
    }

    public function trayRight(): self
    {
        return $this->windowPosition('trayRight');
    }

    public function trayBottomRight(): self
    {
        return $this->windowPosition('trayBottomRight');
    }

    public function trayCenter(): self
    {
        return $this->windowPosition('trayCenter');
    }

    public function trayBottomCenter(): self
    {
        return $this->windowPosition('trayBottomCenter');
    }

    public function topLeft(): self
    {
        return $this->windowPosition('topLeft');
    }

    public function topRight(): self
    {
        return $this->windowPosition('topRight');
    }

    public function bottomLeft(): self
    {
        return $this->windowPosition('bottomLeft');
    }

    public function bottomRight(): self
    {
        return $this->windowPosition('bottomRight');
    }

    public function topCenter(): self
    {
        return $this->windowPosition('topCenter');
    }

    public function bottomCenter(): self
    {
        return $this->windowPosition('bottomCenter');
    }

    public function leftCenter(): self
    {
        return $this->windowPosition('leftCenter');
    }

    public function rightCenter(): self
    {
        return $this->windowPosition('rightCenter');
    }

    public function center(): self
    {
        return $this->windowPosition('center');
    }
}
