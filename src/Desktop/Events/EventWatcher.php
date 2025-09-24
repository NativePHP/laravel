<?php

namespace Native\Desktop\Events;

use Illuminate\Support\Facades\Event;
use Native\Desktop\Client\Client;

class EventWatcher
{
    public function __construct(protected Client $client) {}

    public function register(): void
    {
        Event::listen('*', function (string $eventName, array $data) {
            $event = $data[0] ?? (object) null;

            if (! is_object($event)) {
                return;
            }

            if (! method_exists($event, 'broadcastOn')) {
                return;
            }

            $channels = $event->broadcastOn();

            // Only events dispatched on the nativephp channel
            if (! in_array('nativephp', $channels)) {
                return;
            }

            // Only post custom events to broadcasting endpoint
            if (str_starts_with($eventName, 'Native\\Desktop\\Events')) {
                return;
            }

            $this->client->post('broadcast', [
                'event' => "\\{$eventName}",
                'payload' => $event,
            ]);
        });
    }
}
