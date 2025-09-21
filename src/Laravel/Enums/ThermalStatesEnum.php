<?php

namespace Native\Laravel\Enums;

enum ThermalStatesEnum: string
{
    case UNKNOWN = 'unknown';
    case NOMINAL = 'nominal';
    case FAIR = 'fair';
    case SERIOUS = 'serious';
    case CRITICAL = 'critical';
}
