<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Notification
{
    /**
     * The title of the notification.
     *
     * @var string
     */
    protected string $title;

    /**
     * The body of the notification.
     *
     * @var string
     */
    protected string $body;

    /**
     * The event of the notification.
     *
     * @var string
     */
    protected string $event = '';

    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Create a new notification instance.
     *
     * @return static
     */
    public static function new()
    {
        return new static(new Client());
    }

    /**
     * Set the title of the notification.
     *
     * @param string $title The title of the notification.
     * @return self
     */
    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the event of the notification.
     *
     * @param string $event The event of the notification.
     * @return self
     */
    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Set the body of the notification.
     *
     * @param string $body The body of the notification.
     * @return self
     */
    public function message(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Show the notification.
     *
     * @return void
     */
    public function show(): void
    {
        $this->client->post('notification', [
            'title' => $this->title,
            'body' => $this->body,
            'event' => $this->event,
        ]);
    }
}
