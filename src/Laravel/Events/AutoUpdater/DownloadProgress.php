<?php

namespace Native\Laravel\Events\AutoUpdater;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DownloadProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $total,
        public int $delta,
        public int $transferred,
        public float $percent,
        public int $bytesPerSecond
    ) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
