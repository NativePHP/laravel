<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class App
{
    public function __construct(protected Client $client) {}

    public function focus(): void
    {
        $this->client->post('app/focus');
    }

    public function hide(): void
    {
        $this->client->post('app/hide');
    }

    public function isHidden(): bool
    {
        return $this->client->get('app/is-hidden')->json('is_hidden');
    }

    public function version(): string
    {
        return $this->client->get('app/version')->json('version');
    }

    public function badgeCount($count = null): int
    {
        if ($count === null) {
            return (int) $this->client->get('app/badge-count')->json('count');
        }

        $this->client->post('app/badge-count', [
            'count' => (int) $count,
        ]);

        return (int) $count;
    }

    public function addRecentDocument(string $path): void
    {
        $this->client->post('app/recent-documents', [
            'path' => $path,
        ]);
    }

    public function recentDocuments(): array
    {
        return $this->client->get('app/recent-documents')->json('documents');
    }

    public function clearRecentDocuments(): void
    {
        $this->client->delete('app/recent-documents');
    }
}
