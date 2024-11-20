<?php

namespace Native\Laravel\Enums;

enum RolesEnum: string
{
    case APP_MENU = 'appMenu'; // macOS
    case FILE_MENU = 'fileMenu';
    case EDIT_MENU = 'editMenu';
    case VIEW_MENU = 'viewMenu';
    case WINDOW_MENU = 'windowMenu';
    case HELP = 'help'; // macOS
    case UNDO = 'undo';
    case REDO = 'redo';
    case CUT = 'cut';
    case COPY = 'copy';
    case PASTE = 'paste';
    case PASTE_STYLE = 'pasteAndMatchStyle';
    case RELOAD = 'reload';
    case HIDE = 'hide'; // macOS
    case MINIMIZE = 'minimize';
    case CLOSE = 'close';
    case QUIT = 'quit';
    case TOGGLE_FULL_SCREEN = 'togglefullscreen';
    case TOGGLE_DEV_TOOLS = 'toggleDevTools';
    case ABOUT = 'about';
}
