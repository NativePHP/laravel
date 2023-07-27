const {copySync, removeSync, existsSync} = require("fs-extra");
const {join} = require("path");
const phpBinaryPath = process.env.NATIVEPHP_PHP_BINARY_PATH;
const isArm64 = process.argv.includes('--arm64');
const isWindows = process.argv.includes('--win');
const isLinux = process.argv.includes('--linux');
const certificatePath = process.env.NATIVEPHP_CERTIFICATE_FILE_PATH;

let os = 'mac';
let phpBinaryFilename = 'php';
if (isWindows) {
    phpBinaryFilename += '.exe';
    os = 'win';
}
if (isLinux) {
    os = 'linux';
}

let binaryArch = 'x64';
if (isArm64) {
    binaryArch = 'arm64';
}
if (isWindows || isLinux) {
    binaryArch = 'x64';
}

const binarySrcDir = join(phpBinaryPath, os, binaryArch);
const binaryDestDir = join(__dirname, 'resources/php');

console.log('Binary Source: ', binarySrcDir);
console.log('Binary Filename: ', phpBinaryFilename);

if (phpBinaryPath) {
    try {
        console.log('Copying PHP file(s) from ' + binarySrcDir + ' to ' + binaryDestDir);
        removeSync(binaryDestDir);
        copySync(binarySrcDir, binaryDestDir);


        // If we're building for Windows, copy the php.exe from the dest dir to `php`.
        // This allows the same import command to work on all platforms (same binary filename)
        if (isWindows && existsSync(join(binaryDestDir, phpBinaryFilename))) {
            copySync(join(binaryDestDir, phpBinaryFilename), join(binaryDestDir, 'php'));
        }
    } catch (e) {
        console.log('Error copying PHP binary', e);
    }
}


if (certificatePath) {
    try {
        let certDest = join(__dirname, 'resources', 'cacert.pem');
        copySync(certificatePath, certDest);
        console.log('Copied certificate file to', certDest);
    } catch (e) {
        console.error('Error copying certificate file', e);
    }
}
