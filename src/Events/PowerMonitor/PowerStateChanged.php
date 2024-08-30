<?php

namespace Native\Laravel\Events\PowerMonitor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Laravel\Enums\PowerStatesEnum;

class PowerStateChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PowerStatesEnum $state;

    public function __construct(string $state)
    {
        $this->state = PowerStatesEnum::from($state);
    }

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
