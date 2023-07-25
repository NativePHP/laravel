const {copySync} = require("fs-extra");
const {join} = require("path");
const phpBinaryPath = process.env.NATIVEPHP_PHP_BINARY_PATH;
const isArm64 = process.argv.includes('--arm64');

if (phpBinaryPath) {
    try {
        copySync(join(phpBinaryPath, (isArm64 ? 'arm64' : 'x86'), 'php'), join(__dirname, 'resources', 'php'));
    } catch (e) {
        console.log('Error copying PHP binary', e);
    }
}

