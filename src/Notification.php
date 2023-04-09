<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Notification
{
    protected string $title;

    protected string $body;

    public function __construct(protected Client $client)
    {
    }

    public static function new()
    {
        return new static(new Client());
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function message(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function show(): void
    {
        $this->client->post('notification', [
            'title' => $this->title,
            'body' => $this->body,
        ]);
    }
}
