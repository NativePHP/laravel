<?php

namespace Native\Laravel\Events\App;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ProjectFileChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public readonly string $relativePath) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
