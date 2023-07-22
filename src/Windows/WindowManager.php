<?php

namespace Native\Laravel\Windows;

use Native\Laravel\Client\Client;
use Native\Laravel\Concerns\DetectsWindowId;

class WindowManager
{
    use DetectsWindowId;

    /**
     * Create a new WindowManager instance.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {

    }

    /**
     * Open a new window with the given ID (default ID is 'main').
     *
     * @param string $id The unique identifier for the window (default: 'main').
     * @return PendingOpenWindow The pending open window instance.
     */
    public function open(string $id = 'main')
    {
        return (new PendingOpenWindow($id))->setClient($this->client);
    }

    /**
     * Close the window with the specified ID or the current window if ID is not provided.
     *
     * @param string|null $id The unique identifier for the window to close (optional).
     * If not provided, the ID of the current window will be detected.
     */
    public function close($id = null)
    {
        $this->client->post('window/close', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    /**
     * Get information about the current window.
     *
     * @return object An object containing information about the current window.
     */
    public function current()
    {
        return (object) $this->client->get('window/current')->json();
    }

    /**
     * Resize the window with the specified width and height.
     *
     * @param int $width The new width of the window.
     * @param int $height The new height of the window.
     * @param string|null $id The unique identifier for the window to resize (optional).
     * If not provided, the ID of the current window will be detected.
     */
    public function resize($width, $height, $id = null)
    {
        $this->client->post('window/resize', [
            'id' => $id ?? $this->detectId(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * Move the window to the specified position (x, y).
     *
     * @param int $x The new x-coordinate of the window.
     * @param int $y The new y-coordinate of the window.
     * @param bool $animated Whether to animate the window movement (default: false).
     * @param string|null $id The unique identifier for the window to move (optional).
     * If not provided, the ID of the current window will be detected.
     */
    public function position($x, $y, $animated = false, $id = null)
    {
        $this->client->post('window/resize', [
            'id' => $id ?? $this->detectId(),
            'x' => $x,
            'y' => $y,
            'animate' => $animated,
        ]);
    }

    /**
     * Set the window to always be on top of other windows or remove always on top behavior.
     *
     * @param bool $alwaysOnTop Whether the window should always be on top (default: true).
     * @param string|null $id The unique identifier for the window to set always on top (optional).
     * If not provided, the ID of the current window will be detected.
     */
    public function alwaysOnTop($alwaysOnTop = true, $id = null): void
    {
        $this->client->post('window/always-on-top', [
            'id' => $id ?? $this->detectId(),
            'alwaysOnTop' => $alwaysOnTop,
        ]);
    }
}
