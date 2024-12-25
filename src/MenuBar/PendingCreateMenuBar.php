<?php

namespace Native\Laravel\MenuBar;

class PendingCreateMenuBar extends MenuBar
{
    protected bool $created = false;

    public function __destruct()
    {
        if (! $this->created) {
            $this->now();
        }
    }

    public function now(): void
    {
        $this->client->post('menu-bar/create', $this->toArray());
        $this->created = true;
    }
}
