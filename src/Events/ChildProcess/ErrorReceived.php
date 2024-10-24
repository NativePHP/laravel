<?php

namespace Native\Laravel\Events\ChildProcess;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ErrorReceived implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $alias, public mixed $data) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
