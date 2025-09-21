<?php

namespace Native\Laravel\MenuBar;

class PendingCreateMenuBar extends MenuBar
{
    public function __destruct()
    {
        $this->create();
    }

    protected function create(): void
    {
        $this->client->post('menu-bar/create', $this->toArray());
    }
}
