import {app} from 'electron'
import NativePHP from '@nativephp/electron-plugin'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
// We can use `php` on all platforms because on Windows we copy the php.exe to `php` in electron-builder.js
import phpBinary from '../../resources/php/php?asset&asarUnpack'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate
);
