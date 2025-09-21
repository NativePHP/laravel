<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Notification
{
    public ?string $reference = null;

    protected string $title;

    protected string $body;

    protected string $event = '';

    private bool $hasReply = false;

    private string $replyPlaceholder = '';

    private array $actions = [];

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

    public function hasReply(string $placeholder = ''): self
    {
        $this->hasReply = true;
        $this->replyPlaceholder = $placeholder;

        return $this;
    }

    public function addAction(string $label): self
    {
        $this->actions[] = $label;

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
            'hasReply' => $this->hasReply,
            'replyPlaceholder' => $this->replyPlaceholder,
            'actions' => array_map(fn (string $label) => [
                'type' => 'button',
                'text' => $label,
            ], $this->actions),
        ]);

        $this->reference = $response->json('reference');

        return $this;
    }
}
