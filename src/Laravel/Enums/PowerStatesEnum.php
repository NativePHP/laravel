<?php

namespace Native\Laravel\Enums;

enum PowerStatesEnum: string
{
    case AC = 'on-ac';
    case BATTERY = 'on-battery';
}
