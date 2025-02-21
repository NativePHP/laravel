<?php

namespace Native\Electron\Traits;

trait InstallsAppIcon
{
    public function installIcon()
    {
        @copy(public_path('icon.png'), __DIR__.'/../../resources/js/build/icon.png');
        @copy(public_path('icon.png'), __DIR__.'/../../resources/js/resources/icon.png');
        @copy(public_path('IconTemplate.png'), __DIR__.'/../../resources/js/resources/IconTemplate.png');
        @copy(public_path('IconTemplate@2x.png'), __DIR__.'/../../resources/js/resources/IconTemplate@2x.png');
    }
}
