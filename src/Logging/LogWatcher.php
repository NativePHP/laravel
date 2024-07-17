<?php

namespace Native\Laravel\Logging;

use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Native\Laravel\Client\Client;

class LogWatcher
{
    public function __construct(protected Client $client) {}

    public function register(): void
    {
        Event::listen(MessageLogged::class, function (MessageLogged $message) {
            $payload = [
                'level' => $message->level,
                'message' => $message->message,
                'context' => $message->context,
            ];

            $this->client->post('debug/log', $payload);
        });
    }
}
