<?php

namespace Native\Laravel\Events\App;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationBooted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $path)
    {

    }
}
