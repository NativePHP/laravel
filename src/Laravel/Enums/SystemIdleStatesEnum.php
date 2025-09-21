<?php

namespace Native\Laravel\Enums;

enum SystemIdleStatesEnum: string
{
    case ACTIVE = 'active';
    case IDLE = 'idle';
    case LOCKED = 'locked';
    case UNKNOWN = 'unknown';
}
