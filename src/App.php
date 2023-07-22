<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class App
{
    /**
     * Constructor.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(protected Client $client)
    {
    }

    /**
     * Focus the application window.
     *
     * @return void
     */
    public function focus(): void
    {
        $this->client->post('app/focus');
    }

    /**
     * Hide the application window.
     *
     * @return void
     */
    public function hide(): void
    {
        $this->client->post('app/hide');
    }

    /**
     * Check if the application window is currently hidden.
     *
     * @return bool Returns true if the application window is hidden, otherwise false.
     */
    public function isHidden(): bool
    {
        return $this->client->get('app/is-hidden')->json('is_hidden');
    }

    /**
     * Get the version of the application.
     *
     * @return string The version of the application.
     */
    public function version(): string
    {
        return $this->client->get('app/version')->json('version');
    }

    /**
     * Get or set the badge count for the application icon.
     *
     * @param int|null $count The badge count to be set for the application icon. If null, retrieve the current badge count.
     * @return int The current or newly set badge count for the application icon.
     */
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

    /**
     * Add a recent document to the application.
     *
     * @param string $path The file path of the recent document to be added.
     * @return void
     */
    public function addRecentDocument(string $path): void
    {
        $this->client->post('app/recent-documents', [
            'path' => $path,
        ]);
    }

    /**
     * Get the list of recent documents added to the application.
     *
     * @return array An array containing the file paths of the recent documents.
     */
    public function recentDocuments(): array
    {
        return $this->client->get('app/recent-documents')->json('documents');
    }

    /**
     * Clear the list of recent documents from the application.
     *
     * @return void
     */
    public function clearRecentDocuments(): void
    {
        $this->client->delete('app/recent-documents');
    }
}
