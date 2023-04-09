<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Window
{
    protected string $url = '';
    protected int $width = 400;
    protected int $height = 400;
    protected bool $alwaysOnTop = false;
    protected bool $resizable = true;
    protected string $title = '';

    protected string $id;

    public function __construct(protected Client $client)
    {
        $this->id = Str::uuid();
    }

    public static function new(): static
    {
        return new static(new Client());
    }

    public function close($id = null): void
    {
        $this->client->post('window/close', [
            'id' => $id ?? $this->detectId(),
        ]);
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function width($width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height($height): self
    {
        $this->height = $height;

        return $this;
    }

    public function resizable($resizable = true): static
    {
        $this->resizable = $resizable;

        return $this;
    }

    public function open(): void
    {
        $this->client->post('window/open', [
            'id' => $this->id,
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
            'alwaysOnTop' => $this->alwaysOnTop,
            'resizable' => $this->resizable,
            'title' => $this->title,
        ]);
    }

    public function detectId(): ?string
    {
        $previousUrl = request()->headers->get('Referer');
        $currentUrl = URL::current();

        // Return the _windowId query parameter from either the previous or current URL.
        $parsedUrl = parse_url($previousUrl ?? $currentUrl);
        parse_str($parsedUrl['query'] ?? '', $query);

        return $query['_windowId'] ?? null;
    }

    public function resize($width, $height): void
    {
        $this->client->post('window/resize', [
            'id' => $this->detectId(),
            'width' => $width,
            'height' => $height,
        ]);
    }

    public function alwaysOnTop($alwaysOnTop): void
    {
        $this->client->post('window/always-on-top', [
            'id' => $this->detectId(),
            'alwaysOnTop' => $alwaysOnTop,
        ]);
    }
}
