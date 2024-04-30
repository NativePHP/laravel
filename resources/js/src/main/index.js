import {app} from 'electron'
import NativePHP from '@nativephp/electron-plugin'
import path from 'path'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'

const isWin = process.platform === 'win32';

const phpBinary = path.join(__dirname, '../../resources', isWin ? 'php/php.exe' : 'php/php');

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate
);
