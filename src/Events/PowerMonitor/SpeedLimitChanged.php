<?php

namespace Native\Laravel\Events\PowerMonitor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpeedLimitChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $limit;

    public function __construct(string $limit)
    {
        $this->limit = (int) $limit;
    }

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
