<?php

namespace Native\Laravel\Events\ChildProcess;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessExited implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $alias, public int $code) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
