<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Settings
{
    public function __construct(protected Client $client) {}

    /**
     * Set a value in the settings using the provided key.
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->client->post('settings/'.$key, [
            'value' => $value,
        ]);
    }

    /**
     * Retrieve a value from the settings using the provided key.
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        return $this->client->get('settings/'.$key)->json('value') ?? $default;
    }

    /**
     * Determine if a value exists in the settings for the provided key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->client->get('settings/'.$key)->json('value') !== null;
    }

    /**
     * Remove a value from the settings using the provided key.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget(string $key): void
    {
        $this->client->delete('settings/'.$key);
    }

    /**
     * Clear all settings by deleting them from the storage.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->client->delete('settings/');
    }
}
