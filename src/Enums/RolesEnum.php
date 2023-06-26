<?php

namespace Native\Laravel\Enums;

enum RolesEnum: string
{
    case APP_MENU = 'appMenu';
    case QUIT = 'quit';
    case TOGGLE_FULL_SCREEN = 'togglefullscreen';
    case TOGGLE_DEV_TOOLS = 'toggleDevTools';
}
