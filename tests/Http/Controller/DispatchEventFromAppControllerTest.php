<?php

use Illuminate\Support\Facades\Event;

class TestEvent
{
    public function __construct(public string $test = '') {}
}

it('dispatches an event', function () {
    Event::fake();

    $this->withoutMiddleware()
        ->post('_native/api/events', [
            'event' => TestEvent::class,
        ])
        ->assertOk();

    Event::assertDispatched(TestEvent::class);
});

// Since 45b7ccfcb86ebf35be35c1eb7fbb9f05a224448f nonexistent classes are handled as string events
it('dispatches a string event', function () {
    Event::fake();

    $this->withoutMiddleware()
        ->post('_native/api/events', [
            'event' => 'some-event-that-is-no-class',
        ]);

    Event::assertDispatched('some-event-that-is-no-class');
});

it('passes the payload to the event', function () {
    Event::fake();

    $this->withoutMiddleware()
        ->post('_native/api/events', [
            'event' => TestEvent::class,
            'payload' => [
                'test' => 'Some payload string',
            ],
        ]);

    Event::assertDispatched(TestEvent::class, function ($event) {
        return $event->test === 'Some payload string';
    });
});
