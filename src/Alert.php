<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Alert
{
    protected ?string $type = null;

    protected ?string $title = null;

    protected ?string $detail = null;

    protected ?array $buttons = null;

    protected ?int $defaultId = null;

    protected ?int $cancelId = null;

    final public function __construct(protected Client $client) {}

    public static function new(): self
    {
        return new static(new Client);
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function detail(string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function defaultId(int $defaultId): self
    {
        $this->defaultId = $defaultId;

        return $this;
    }

    public function cancelId(int $cancelId): self
    {
        $this->cancelId = $cancelId;

        return $this;
    }

    public function show(string $message): int
    {
        $response = $this->client->post('alert/message', [
            'message' => $message,
            'type' => $this->type,
            'title' => $this->title,
            'detail' => $this->detail,
            'buttons' => $this->buttons,
            'defaultId' => $this->defaultId,
            'cancelId' => $this->cancelId,
        ]);

        return (int) $response->json('result');
    }

    public function error(string $title, string $message): bool
    {
        $response = $this->client->post('alert/error', [
            'title' => $title,
            'message' => $message,
        ]);

        return (bool) $response->json('result');
    }
}
