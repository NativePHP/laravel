<?php

namespace Native\Laravel\Windows;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\DetectsWindowId;

class WindowManager
{
    use DetectsWindowId;

    public function __construct(protected Client $client) {}

    public function open(string $id = 'main')
    {
        return (new PendingOpenWindow($id))->setClient($this->client);
    }

    public function close($id = null)
    {
        $this->client->post('window/close', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function hide($id = null)
    {
        return $this->client->post('window/hide', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function current()
    {
        return (object) $this->client->get('window/current')->json();
    }

    public function resize($width, $height, $id = null)
    {
        $this->client->post('window/resize', [
            'id' => $id ?? $this->detectId(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function position($x, $y, $animated = false, $id = null)
    {
        $this->client->post('window/position', [
            'id' => $id ?? $this->detectId(),
            'x' => $x,
            'y' => $y,
            'animate' => $animated,
        ]);
    }

    public function alwaysOnTop($alwaysOnTop = true, $id = null): void
    {
        $this->client->post('window/always-on-top', [
            'id' => $id ?? $this->detectId(),
            'alwaysOnTop' => $alwaysOnTop,
        ]);
    }

    public function maximize($id = null): void
    {
        $this->client->post('window/maximize', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function minimize($id = null): void
    {
        $this->client->post('window/minimize', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function reload($id = null): void
    {
        $this->client->post('window/reload', [
            'id' => $id ?? $this->detectId(),
        ]);
    }
}
