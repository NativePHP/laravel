<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class System
{
    public function __construct(protected Client $client)
    {
    }

    public function canPromptTouchID(): bool
    {
        return $this->client->get('system/can-prompt-touch-id')->json('result');
    }

    public function promptTouchID(string $reason): bool
    {
        return $this->client->post('system/prompt-touch-id', [
            'reason' => $reason,
        ])->successful();
    }
}
