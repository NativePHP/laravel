<?php

namespace Native\Laravel\MenuBar;

class PendingCreateMenuBar extends MenuBar
{
    protected bool $created = false;

    public function __destruct()
    {
        if (! $this->created) {
            $this->create();
        }
    }

    public function create(): void
    {
        $this->client->post('menu-bar/create', $this->toArray());
        $this->created = true;
    }
}
