<?php

namespace Native\Electron\Traits;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\note;

trait InstallsAppIcon
{
    public function installIcon()
    {
        intro('Copying app icons...');

        @copy(public_path('icon.png'), __DIR__.'/../../resources/js/resources/icon.png');
        @copy(public_path('IconTemplate.png'), __DIR__.'/../../resources/js/resources/IconTemplate.png');
        @copy(public_path('IconTemplate@2x.png'), __DIR__.'/../../resources/js/resources/IconTemplate@2x.png');

        note('App icons copied');
    }
}
