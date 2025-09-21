<?php

namespace Native\Laravel\Windows;

class PendingOpenWindow extends Window
{
    public function __destruct()
    {
        $this->open();
    }

    protected function open(): void
    {
        $this->client->post('window/open', $this->toArray());

        foreach ($this->afterOpenCallbacks as $cb) {
            $cb($this);
        }
    }
}
