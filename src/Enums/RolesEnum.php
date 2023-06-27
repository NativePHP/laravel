<?php

namespace Native\Laravel\Enums;

enum RolesEnum: string
{
    case APP_MENU = 'appMenu';
    case FILE_MENU = 'fileMenu';
    case EDIT_MENU = 'editMenu';
    case VIEW_MENU = 'viewMenu';
    case WINDOW_MENU = 'windowMenu';
    case QUIT = 'quit';
    case TOGGLE_FULL_SCREEN = 'togglefullscreen';
    case TOGGLE_DEV_TOOLS = 'toggleDevTools';
}
