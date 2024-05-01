import {app} from 'electron'
import NativePHP from '@nativephp/electron-plugin'
import path from 'path'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'

let phpBinary = process.platform === 'win32' ? 'php.exe' : 'php';

phpBinary = path.join(__dirname, '../../resources/php', phpBinary).replace("app.asar", "app.asar.unpacked");

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate
);
