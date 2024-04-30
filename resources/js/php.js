const fs = require("fs");
const {copySync, removeSync, existsSync, ensureDirSync} = require("fs-extra");
const {join} = require("path");
const unzip = require("yauzl");

const isBuilding = process.env.NATIVEPHP_BUILDING;
const phpBinaryPath = process.env.NATIVEPHP_PHP_BINARY_PATH;
const phpVersion = process.env.NATIVEPHP_PHP_BINARY_VERSION;
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

const phpVersionZip = 'php-' + phpVersion + '.zip';
const binarySrcDir = join(phpBinaryPath, targetOs, binaryArch, phpVersionZip);
const binaryDestDir = join(__dirname, 'resources/php');

console.log('Binary Source: ', binarySrcDir);
console.log('Binary Filename: ', phpBinaryFilename);
console.log('PHP version: ' + phpVersion);

if (phpBinaryPath) {
    try {
        console.log('Unzipping PHP binary from ' + binarySrcDir + ' to ' + binaryDestDir);
        removeSync(binaryDestDir);

        ensureDirSync(binaryDestDir);

        // Unzip the files
        unzip.open(binarySrcDir, {lazyEntries: true}, function (err, zipfile) {
            if (err) throw err;
            zipfile.readEntry();
            zipfile.on("entry", function (entry) {
                zipfile.openReadStream(entry, function (err, readStream) {
                    if (err) throw err;

                    const binaryPath = join(binaryDestDir, phpBinaryFilename);
                    const writeStream = fs.createWriteStream(binaryPath);

                    readStream.pipe(writeStream);

                    writeStream.on("close", function() {
                        console.log('Copied PHP binary to ', binaryPath);

                        // Add execute permissions
                        fs.chmod(binaryPath, 0o755, (err) => {
                            if (err) {
                                console.log(`Error setting permissions: ${err}`);
                            }
                        });

                        zipfile.readEntry();
                    });
                });
            });
        });
    } catch (e) {
        console.error('Error copying PHP binary', e);
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
