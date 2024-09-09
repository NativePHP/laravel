<?php

namespace Native\Laravel\Events;

use Illuminate\Support\Facades\Event;
use Native\Laravel\Client\Client;

class EventWatcher
{
    public function __construct(protected Client $client) {}

    public function register(): void
    {
        Event::listen('*', function (string $eventName, array $data) {

            $event = $data[0] ?? null;

            if (! method_exists($event, 'broadcastOn')) {
                return;
            }

            $channels = $event->broadcastOn();

            if(! in_array('nativephp', $channels)) {
                return;
            }

            $this->client->post('debug/broadcast', ['event' => $event]);
        });
    }
}
