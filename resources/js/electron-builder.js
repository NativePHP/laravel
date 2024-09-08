const {copySync, removeSync, writeJsonSync, existsSync} = require("fs-extra");
const {join} = require("path");
const os = require('os');
const {mkdtempSync} = require("fs");
const {execSync} = require("child_process");
const isBuilding = process.env.NATIVEPHP_BUILDING;
const appId = process.env.NATIVEPHP_APP_ID;
const appName = process.env.NATIVEPHP_APP_NAME;
const fileName = process.env.NATIVEPHP_APP_FILENAME;
const appVersion = process.env.NATIVEPHP_APP_VERSION;
const appUrl = process.env.APP_URL;
const appAuthor = process.env.NATIVEPHP_APP_AUTHOR;
const deepLinkProtocol = process.env.NATIVEPHP_DEEPLINK_SCHEME;

// Since we do not copy the php executable here, we only need these for building
const isWindows = process.argv.includes('--win');
const isLinux = process.argv.includes('--linux');
const isDarwin = process.argv.includes('--mac');

let targetOs;

if (isWindows) {
    targetOs = 'win';
}
if (isLinux) {
    targetOs = 'linux';
}
// Use of isDarwin
if (isDarwin) {
    targetOs = 'mac';
}


let updaterConfig = {};

// We wouldn't need these since its not representing the target platform
console.log("Arch: ", process.arch)
console.log("Platform: ", process.platform)
try {
    updaterConfig = process.env.NATIVEPHP_UPDATER_CONFIG;
    updaterConfig = JSON.parse(updaterConfig);
} catch (e) {
    updaterConfig = {};
}

if (isBuilding) {

    console.log('=====================');
    console.log('Building for ' + targetOs);
    console.log('=====================');
    console.log('updater config', updaterConfig);
    console.log('=====================');

    try {
        const appPath = join(__dirname, 'resources', 'app');

        removeSync(appPath);

        // As we can't copy into a subdirectory of ourself we need to copy to a temp directory
        let tmpDir = mkdtempSync(join(os.tmpdir(), 'nativephp'));

        copySync(process.env.APP_PATH, tmpDir, {
            overwrite: true,
            dereference: true,
            filter: (src, dest) => {
                let skip = [
                    // Skip .git and Dev directories
                    join(process.env.APP_PATH, '.git'),
                    join(process.env.APP_PATH, 'docker'),
                    join(process.env.APP_PATH, 'packages'),

                    // Only needed for local testing
                    join(process.env.APP_PATH, 'vendor', 'nativephp', 'electron', 'vendor'),
                    join(process.env.APP_PATH, 'vendor', 'nativephp', 'laravel', 'vendor'),

                    join(process.env.APP_PATH, 'vendor', 'nativephp', 'php-bin'),
                    join(process.env.APP_PATH, 'vendor', 'nativephp', 'electron', 'bin'),
                    join(process.env.APP_PATH, 'vendor', 'nativephp', 'electron', 'resources'),
                    join(process.env.APP_PATH, 'node_modules'),
                    join(process.env.APP_PATH, 'dist'),
                ];

                let shouldSkip = false;
                skip.forEach((path) => {
                    if (src.indexOf(path) === 0) {
                        shouldSkip = true;
                    }
                });

                return !shouldSkip;
            }
        });

        copySync(tmpDir, appPath);

        // Electron build removes empty folders, so we have to create dummy files
        // dotfiles unfortunately don't work.
        writeJsonSync(join(appPath, 'storage', 'framework', 'cache', '_native.json'), {})
        writeJsonSync(join(appPath, 'storage', 'framework', 'sessions', '_native.json'), {})
        writeJsonSync(join(appPath, 'storage', 'framework', 'testing', '_native.json'), {})
        writeJsonSync(join(appPath, 'storage', 'framework', 'views', '_native.json'), {})
        writeJsonSync(join(appPath, 'storage', 'app', 'public', '_native.json'), {})
        writeJsonSync(join(appPath, 'storage', 'logs', '_native.json'), {})

        removeSync(tmpDir);

        console.log('=====================');
        console.log('Copied app to resources');
        console.log(join(process.env.APP_PATH, 'dist'));
        console.log('=====================');

        const artisanPath = join(appPath, 'artisan');
        // We'll use the default PATH PHP binary here, as we can cross-compile for all platforms. This shouldn't be changed.
        execSync(`php ${artisanPath} native:minify ${appPath}`);
    } catch (e) {
        console.error('=====================');
        console.error('Error copying app to resources');
        console.error(e);
        console.error('=====================');
    }

}

module.exports = {
    appId: appId,
    productName: appName,
    directories: {
        buildResources: 'build',
        output: isBuilding ? join(process.env.APP_PATH, 'dist') : undefined,
    },
    files: [
        '!**/.vscode/*',
        '!src/*',
        '!electron.vite.config.{js,ts,mjs,cjs}',
        '!{.eslintignore,.eslintrc.cjs,.prettierignore,.prettierrc.yaml,dev-app-update.yml,CHANGELOG.md,README.md}',
        '!{.env,.env.*,.npmrc,pnpm-lock.yaml}',
    ],
    asarUnpack: [
        'resources/**',
    ],
    afterSign: 'build/notarize.js',
    win: {
        executableName: fileName,
    },
    nsis: {
        artifactName: appName + '-${version}-setup.${ext}',
        shortcutName: '${productName}',
        uninstallDisplayName: '${productName}',
        createDesktopShortcut: 'always',
    },
    protocols: {
        name: deepLinkProtocol,
        schemes: [deepLinkProtocol],
    },
    mac: {
        entitlementsInherit: 'build/entitlements.mac.plist',
        artifactName: appName + '-${version}-${arch}.${ext}',
        extendInfo: {
            NSCameraUsageDescription:
                "Application requests access to the device's camera.",
            NSMicrophoneUsageDescription:
                "Application requests access to the device's microphone.",
            NSDocumentsFolderUsageDescription:
                "Application requests access to the user's Documents folder.",
            NSDownloadsFolderUsageDescription:
                "Application requests access to the user's Downloads folder.",
        },
    },
    dmg: {
        artifactName: appName + '-${version}-${arch}.${ext}',
    },
    linux: {
        target: ['AppImage', 'deb'],
        maintainer: appUrl,
        category: 'Utility',
    },
    appImage: {
        artifactName: appName + '-${version}.${ext}',
    },
    npmRebuild: false,
    publish: updaterConfig,
    extraMetadata: {
        name: fileName,
        homepage: appUrl,
        version: appVersion,
        author: appAuthor,
    }
};
