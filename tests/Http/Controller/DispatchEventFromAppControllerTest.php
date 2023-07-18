<?php

use Illuminate\Support\Facades\Event;

class TestEvent
{
    public function __construct(public string $test = '')
    {

    }
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

it('dispatches no event in case it does not exist', function () {
    Event::fake();

    $this->withoutMiddleware()
        ->post('_native/api/events', [
            'event' => InvalidEvent::class,
        ]);

    Event::assertNotDispatched(InvalidEvent::class);
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
