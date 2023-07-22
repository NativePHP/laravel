<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Settings
{
    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Set a value for the specified setting key.
     *
     * @param mixed $key The key of the setting to be updated.
     * @param mixed $value The new value to be set for the setting.
     * @return void
     */
    public function set($key, $value): void
    {
        $this->client->post('settings/'.$key, [
            'value' => $value,
        ]);
    }

    /**
     * Get the value for the specified setting key.
     *
     * @param mixed $key The key of the setting to retrieve.
     * @param mixed $default Optional. The default value to return if the setting does not exist.
     * @return mixed The value of the setting if found, otherwise the default value (or null if not provided).
     */
    public function get($key, $default = null): mixed
    {
        return $this->client->get('settings/'.$key)->json('value') ?? $default;
    }
}
