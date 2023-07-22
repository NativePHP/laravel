<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class System
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
     * Check if the system can prompt for Touch ID.
     *
     * @return bool
     */
    public function canPromptTouchID(): bool
    {
        return $this->client->get('system/can-prompt-touch-id')->json('result');
    }

    /**
     * Prompt for Touch ID with a given reason.
     *
     * @param string $reason The reason for prompting Touch ID.
     * @return bool
     */
    public function promptTouchID(string $reason): bool
    {
        return $this->client->post('system/prompt-touch-id', [
            'reason' => $reason,
        ])->successful();
    }
}
