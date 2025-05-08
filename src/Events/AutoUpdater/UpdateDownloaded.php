<?php

namespace Native\Laravel\Events\AutoUpdater;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateDownloaded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $version, public string $downloadedFile, public string $releaseDate, public ?string $releaseNotes, public ?string $releaseName) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
