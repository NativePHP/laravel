<?php

namespace Native\Laravel\Windows;

class PendingOpenWindow extends Window
{
    /**
     * Destructor.
     * Automatically opens the pending window when the object is destroyed.
     */
    public function __destruct()
    {
        $this->open();
    }

    /**
     * Open the pending window.
     *
     * @return void
     */
    protected function open(): void
    {
        $this->client->post('window/open', $this->toArray());
    }
}
