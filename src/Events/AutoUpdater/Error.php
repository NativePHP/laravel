<?php

namespace Native\Desktop\Events\AutoUpdater;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Error implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $name,
        public string $message,
        public ?string $stack = null,
    ) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
