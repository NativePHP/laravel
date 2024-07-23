const { log } = require("console");
const fs = require("fs");
const {copySync, removeSync, existsSync, ensureDirSync} = require("fs-extra");
const {join} = require("path");
const { exit } = require("process");
const unzip = require("yauzl");

const isBuilding = Boolean(process.env.NATIVEPHP_BUILDING);
const phpBinaryPath = process.env.NATIVEPHP_PHP_BINARY_PATH;
const phpVersion = process.env.NATIVEPHP_PHP_BINARY_VERSION;
const certificatePath = process.env.NATIVEPHP_CERTIFICATE_FILE_PATH;

// Differentiates for Serving and Building
const isArm64 = isBuilding ? process.argv.includes('--arm64') : process.arch.includes('arm64');
const isWindows = isBuilding ?  process.argv.includes('--win') : process.platform.includes('win32');
const isLinux = isBuilding ?  process.argv.includes('--linux') : process.platform.includes('linux');
const isDarwin = isBuilding ?  process.argv.includes('--mac') : process.platform.includes('darwin');

// false because string mapping is done in is{OS} checks
const platform = {
    os: false,
    arch: false,
    phpBinary: 'php'
};

if (isWindows) {
    platform.os = 'win';
    platform.phpBinary += '.exe';
    platform.arch = 'x64';
}

if (isLinux) {
    platform.os = 'linux';
    platform.arch = 'x64';
}

if (isDarwin) {
    platform.os = 'mac';
    platform.arch = 'x86';
}

if (isArm64) {
    platform.arch = 'arm64';
}

// isBuilding overwrites platform to the desired architecture
if (isBuilding) {
    // Only one will be used by the configured build commands in package.json
    platform.arch = process.argv.includes('--x64') ? 'x64' : platform.arch;
    platform.arch = process.argv.includes('--x86') ? 'x86' : platform.arch;
    platform.arch = process.argv.includes('--arm64') ? 'arm64' : platform.arch;
}

const phpVersionZip = 'php-' + phpVersion + '.zip';
const binarySrcDir = join(phpBinaryPath, platform.os, platform.arch, phpVersionZip);
const binaryDestDir = join(__dirname, 'resources/php');

console.log('Binary Source: ', binarySrcDir);
console.log('Binary Filename: ', platform.phpBinary);
console.log('PHP version: ' + phpVersion);

if (platform.phpBinary) {
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

                    const binaryPath = join(binaryDestDir, platform.phpBinary);
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
