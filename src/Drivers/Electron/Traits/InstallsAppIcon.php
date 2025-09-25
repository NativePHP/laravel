<?php

namespace Native\Desktop\Drivers\Electron\Traits;

use Native\Desktop\Drivers\Electron\ElectronServiceProvider;

trait InstallsAppIcon
{
    public function installIcon()
    {
        @copy(public_path('icon.png'), ElectronServiceProvider::ELECTRON_PATH.'/build/icon.png');
        @copy(public_path('icon.png'), ElectronServiceProvider::ELECTRON_PATH.'/resources/icon.png');
        @copy(public_path('icon.ico'), ElectronServiceProvider::ELECTRON_PATH.'/build/icon.ico');
        @copy(public_path('icon.ico'), ElectronServiceProvider::ELECTRON_PATH.'/resources/icon.ico');
        @copy(public_path('icon.icns'), ElectronServiceProvider::ELECTRON_PATH.'/build/icon.icns');
        @copy(public_path('icon.icns'), ElectronServiceProvider::ELECTRON_PATH.'/resources/icon.icns');
        @copy(public_path('IconTemplate.png'), ElectronServiceProvider::ELECTRON_PATH.'/resources/IconTemplate.png');
        @copy(public_path('IconTemplate@2x.png'), ElectronServiceProvider::ELECTRON_PATH.'/resources/IconTemplate@2x.png');
    }
}
