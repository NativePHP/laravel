<?php

namespace Native\Laravel\Concerns;

trait HasDimensions
{
    protected int $width = 400;

    protected int $height = 400;

    protected int $minWidth = 0;

    protected int $minHeight = 0;

    protected int $maxWidth = 0;

    protected int $maxHeight = 0;

    protected $x;

    protected $y;

    public function width($width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height($height): self
    {
        $this->height = $height;

        return $this;
    }

    public function minWidth($width): self
    {
        $this->minWidth = $width;

        return $this;
    }

    public function minHeight($height): self
    {
        $this->minHeight = $height;

        return $this;
    }

    public function maxWidth($width): self
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function maxHeight($height): self
    {
        $this->maxHeight = $height;

        return $this;
    }

    public function position($x, $y): self
    {
        $this->x = $x;
        $this->y = $y;

        return $this;
    }
}
