<?php

namespace Native\Laravel\Events\AutoUpdater;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateCancelled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $version,
        public array $files,
        public string $releaseDate,
        public ?string $releaseName,
        public string|array|null $releaseNotes,
        public ?int $stagingPercentage,
        public ?string $minimumSystemVersion,
    ) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
