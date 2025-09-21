<?php

namespace Native\Laravel;

use Native\Laravel\Client\Client;

class AutoUpdater
{
    public function __construct(protected Client $client) {}

    public function checkForUpdates(): void
    {
        $this->client->post('auto-updater/check-for-updates');
    }

    public function quitAndInstall(): void
    {
        $this->client->post('auto-updater/quit-and-install');
    }

    public function downloadUpdate(): void
    {
        $this->client->post('auto-updater/download-update');
    }
}
