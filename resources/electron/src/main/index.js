import {app} from 'electron'
import NativePHP from '#plugin'
import path from 'path'

const buildPath = path.resolve(import.meta.dirname, import.meta.env.MAIN_VITE_NATIVEPHP_BUILD_PATH);

const defaultIcon = path.join(buildPath, 'icon.png')
const certificate = path.join(buildPath, 'cacert.pem')

const executable = process.platform === 'win32' ? 'php.exe' : 'php';
const phpBinary = path.join(buildPath,'php', executable);

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate
);
