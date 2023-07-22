import NativePHP from 'nativephp-electron'
import {app} from 'electron'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
import phpBinary from '../../resources/php?asset&asarUnpack'

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app, 
    defaultIcon, 
    phpBinary, 
    certificate
);
