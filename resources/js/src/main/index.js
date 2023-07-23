// import NativePHP from 'nativephp-electron'

import NativePHP from '@nativephp/electron-plugin'
import {app} from 'electron'

import NativePHP from '@nativephp/electron-plugin'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
import phpBinary from '../../resources/php?asset&asarUnpack'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'

// Probably still need to improve this platform awareness
let phpBinary;
if (process.platform === 'win32') {
	phpBinary = import('../../resources/php/php.exe?asset&asarUnpack');
} else {
	phpBinary = import('../../resources/php/php?asset&asarUnpack');
}

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app, 
    defaultIcon, 
    phpBinary, 
    certificate
);
