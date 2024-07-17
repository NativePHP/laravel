<?php

namespace Native\Laravel\Events\App;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationBooted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct() {}
}
