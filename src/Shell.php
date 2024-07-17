<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class Shell
{
    public function __construct(protected Client $client) {}

    public function showInFolder(string $path): void
    {
        $this->client->post('shell/show-item-in-folder', [
            'path' => $path,
        ]);
    }

    public function openFile(string $path): string
    {
        return $this->client->post('shell/open-item', [
            'path' => $path,
        ])->json('result');
    }

    public function trashFile(string $path): void
    {
        $this->client->delete('shell/trash-item', [
            'path' => $path,
        ]);
    }

    public function openExternal(string $url): void
    {
        $this->client->post('shell/open-external', [
            'url' => $url,
        ]);
    }
}
