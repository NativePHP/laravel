<?php

namespace Native\Desktop\Drivers\Electron\Traits;

use Native\Desktop\Drivers\Electron\ElectronServiceProvider;

trait InstallsAppIcon
{
    public function installIcon()
    {
        // Copy to Electron project
        @copy(public_path('icon.png'), ElectronServiceProvider::electronPath('build/icon.png'));
        @copy(public_path('icon.ico'), ElectronServiceProvider::electronPath('build/icon.ico'));
        @copy(public_path('icon.icns'), ElectronServiceProvider::electronPath('build/icon.icns'));

        // Copy to asar archive
        @copy(public_path('icon.png'), ElectronServiceProvider::buildPath('icon.png'));
        @copy(public_path('icon.ico'), ElectronServiceProvider::buildPath('icon.ico'));
        @copy(public_path('icon.icns'), ElectronServiceProvider::buildPath('icon.icns'));

        @copy(public_path('IconTemplate.png'), ElectronServiceProvider::buildPath('IconTemplate.png'));
        @copy(public_path('IconTemplate@2x.png'), ElectronServiceProvider::buildPath('IconTemplate@2x.png'));
    }
}
