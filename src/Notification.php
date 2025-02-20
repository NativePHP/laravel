<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Notification
{
    protected ?string $reference = null;

    protected string $title;

    protected string $body;

    protected string $event = '';

    final public function __construct(protected Client $client)
    {
        $this->title = config('app.name');
    }

    public static function new()
    {
        return new static(new Client);
    }

    public function reference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function message(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function show(): self
    {
        $response = $this->client->post('notification', [
            'reference' => $this->reference,
            'title' => $this->title,
            'body' => $this->body,
            'event' => $this->event,
        ]);

        $this->reference = $response->json('reference');

        return $this;
    }
}
