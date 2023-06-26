<?php

namespace Native\Laravel\Concerns;

trait HasUrl
{
    protected string $url = '';

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function route(string $route): self
    {
        $this->url = route($route);

        return $this;
    }
}
