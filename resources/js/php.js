const {copySync, removeSync, existsSync} = require("fs-extra");
const {join} = require("path");
const isBuilding = process.env.NATIVEPHP_BUILDING;
const phpBinaryPath = process.env.NATIVEPHP_PHP_BINARY_PATH;
const certificatePath = process.env.NATIVEPHP_CERTIFICATE_FILE_PATH;

// Differentiates for Serving and Building
const isArm64 = isBuilding ? process.argv.includes('--arm64') : process.platform.includes('arm64') ;
const isWindows = isBuilding ?  process.argv.includes('--win') : process.platform.includes('win32');
const isLinux = isBuilding ?  process.argv.includes('--linux') : process.platform.includes('linux');
const isDarwin = isBuilding ?  process.argv.includes('--mac') : process.platform.includes('darwin');

let targetOs;
let binaryArch = 'x64';
let phpBinaryFilename = 'php';

if (isWindows) {
    targetOs = 'win';
    phpBinaryFilename += '.exe';
}
if (isLinux) {
    targetOs = 'linux';
}
// Use of isDarwin
if (isDarwin) {
    targetOs = 'mac';
    binaryArch = 'x86';
}
if (isArm64) {
    binaryArch = 'arm64';
}



const binarySrcDir = join(phpBinaryPath, targetOs, binaryArch);
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
