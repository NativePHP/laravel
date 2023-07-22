<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class GlobalShortcut
{
    /**
     * The key associated with the global shortcut.
     *
     * @var string
     */
    protected string $key;

    /**
     * The event associated with the global shortcut.
     *
     * @var string
     */
    protected string $event;

    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Set the key for the global shortcut.
     *
     * @param string $key The key to associate with the global shortcut.
     * @return self Returns the current instance of the GlobalShortcut.
     */
    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Set the event for the global shortcut.
     *
     * @param string $event The event to associate with the global shortcut.
     * @return self Returns the current instance of the GlobalShortcut class.
     */
    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Register the global shortcut with the specified key and event.
     *
     * @return void
     */
    public function register(): void
    {
        $this->client->post('global-shortcuts', [
            'key' => $this->key,
            'event' => $this->event,
        ]);
    }

    /**
     * Unregister the global shortcut associated with the specified key.
     *
     * @return void
     */
    public function unregister(): void
    {
        $this->client->delete('global-shortcuts', [
            'key' => $this->key,
        ]);
    }
}
