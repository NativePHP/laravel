var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { mkdirSync, statSync, writeFileSync, existsSync } from 'fs';
import fs_extra from 'fs-extra';
const { copySync, mkdirpSync } = fs_extra;
import Store from 'electron-store';
import { promisify } from 'util';
import { join } from 'path';
import { app } from 'electron';
import { execFile, spawn, spawnSync } from 'child_process';
import state from "./state.js";
import getPort, { portNumbers } from 'get-port';
const storagePath = join(app.getPath('userData'), 'storage');
const databasePath = join(app.getPath('userData'), 'database');
const databaseFile = join(databasePath, 'database.sqlite');
const bootstrapCache = join(app.getPath('userData'), 'bootstrap', 'cache');
const argumentEnv = getArgumentEnv();
const appPath = getAppPath();
mkdirpSync(bootstrapCache);
mkdirpSync(join(storagePath, 'logs'));
mkdirpSync(join(storagePath, 'framework', 'cache'));
mkdirpSync(join(storagePath, 'framework', 'sessions'));
mkdirpSync(join(storagePath, 'framework', 'views'));
mkdirpSync(join(storagePath, 'framework', 'testing'));
function runningSecureBuild() {
    return existsSync(join(appPath, 'build', '__nativephp_app_bundle'))
        && process.env.NODE_ENV !== 'development';
}
function shouldMigrateDatabase(store) {
    return store.get('migrated_version') !== app.getVersion()
        && process.env.NODE_ENV !== 'development';
}
function shouldOptimize(store) {
    return process.env.NODE_ENV !== 'development';
}
function getPhpPort() {
    return __awaiter(this, void 0, void 0, function* () {
        return yield getPort({
            host: '127.0.0.1',
            port: portNumbers(8100, 9000)
        });
    });
}
function retrievePhpIniSettings() {
    return __awaiter(this, void 0, void 0, function* () {
        const env = getDefaultEnvironmentVariables();
        const phpOptions = {
            cwd: appPath,
            env
        };
        let command = ['artisan', 'native:php-ini'];
        if (runningSecureBuild()) {
            command.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
        }
        return yield promisify(execFile)(state.php, command, phpOptions);
    });
}
function retrieveNativePHPConfig() {
    return __awaiter(this, void 0, void 0, function* () {
        const env = getDefaultEnvironmentVariables();
        const phpOptions = {
            cwd: appPath,
            env
        };
        let command = ['artisan', 'native:config'];
        if (runningSecureBuild()) {
            command.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
        }
        return yield promisify(execFile)(state.php, command, phpOptions);
    });
}
function callPhp(args, options, phpIniSettings = {}) {
    if (args[0] === 'artisan' && runningSecureBuild()) {
        args.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }
    let iniSettings = Object.assign(getDefaultPhpIniSettings(), phpIniSettings);
    Object.keys(iniSettings).forEach(key => {
        args.unshift('-d', `${key}=${iniSettings[key]}`);
    });
    if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
        console.log('Calling PHP', state.php, args);
    }
    return spawn(state.php, args, {
        cwd: options.cwd,
        env: Object.assign(Object.assign({}, process.env), options.env),
    });
}
function callPhpSync(args, options, phpIniSettings = {}) {
    if (args[0] === 'artisan' && runningSecureBuild()) {
        args.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }
    let iniSettings = Object.assign(getDefaultPhpIniSettings(), phpIniSettings);
    Object.keys(iniSettings).forEach(key => {
        args.unshift('-d', `${key}=${iniSettings[key]}`);
    });
    if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
        console.log('Calling PHP', state.php, args);
    }
    return spawnSync(state.php, args, {
        cwd: options.cwd,
        env: Object.assign(Object.assign({}, process.env), options.env)
    });
}
function getArgumentEnv() {
    const envArgs = process.argv.filter(arg => arg.startsWith('--env.'));
    const env = {};
    envArgs.forEach(arg => {
        const [key, value] = arg.slice(6).split('=');
        env[key] = value;
    });
    return env;
}
function getAppPath() {
    let appPath = join(import.meta.dirname, '../../resources/app/').replace('app.asar', 'app.asar.unpacked');
    if (process.env.NODE_ENV === 'development' || argumentEnv.TESTING == 1) {
        appPath = process.env.APP_PATH || argumentEnv.APP_PATH;
    }
    return appPath;
}
function ensureAppFoldersAreAvailable() {
    console.log('Copying storage folder...');
    console.log('Storage path:', storagePath);
    if (!existsSync(storagePath) || process.env.NODE_ENV === 'development') {
        console.log("App path:", appPath);
        copySync(join(appPath, 'storage'), storagePath);
    }
    mkdirSync(databasePath, { recursive: true });
    try {
        statSync(databaseFile);
    }
    catch (error) {
        writeFileSync(databaseFile, '');
    }
}
function startScheduler(secret, apiPort, phpIniSettings = {}) {
    const env = getDefaultEnvironmentVariables(secret, apiPort);
    const phpOptions = {
        cwd: appPath,
        env
    };
    return callPhp(['artisan', 'schedule:run'], phpOptions, phpIniSettings);
}
function getPath(name) {
    try {
        return app.getPath(name);
    }
    catch (error) {
        return '';
    }
}
function getDefaultEnvironmentVariables(secret, apiPort) {
    let variables = {
        APP_ENV: process.env.NODE_ENV === 'development' ? 'local' : 'production',
        APP_DEBUG: process.env.NODE_ENV === 'development' ? 'true' : 'false',
        LARAVEL_STORAGE_PATH: storagePath,
        NATIVEPHP_RUNNING: 'true',
        NATIVEPHP_STORAGE_PATH: storagePath,
        NATIVEPHP_DATABASE_PATH: databaseFile,
        NATIVEPHP_USER_HOME_PATH: getPath('home'),
        NATIVEPHP_APP_DATA_PATH: getPath('appData'),
        NATIVEPHP_USER_DATA_PATH: getPath('userData'),
        NATIVEPHP_DESKTOP_PATH: getPath('desktop'),
        NATIVEPHP_DOCUMENTS_PATH: getPath('documents'),
        NATIVEPHP_DOWNLOADS_PATH: getPath('downloads'),
        NATIVEPHP_MUSIC_PATH: getPath('music'),
        NATIVEPHP_PICTURES_PATH: getPath('pictures'),
        NATIVEPHP_VIDEOS_PATH: getPath('videos'),
        NATIVEPHP_RECENT_PATH: getPath('recent'),
    };
    if (secret && apiPort) {
        variables.NATIVEPHP_API_URL = `http://localhost:${apiPort}/api/`;
        variables.NATIVEPHP_SECRET = secret;
    }
    if (runningSecureBuild()) {
        variables.APP_SERVICES_CACHE = join(bootstrapCache, 'services.php');
        variables.APP_PACKAGES_CACHE = join(bootstrapCache, 'packages.php');
        variables.APP_CONFIG_CACHE = join(bootstrapCache, 'config.php');
        variables.APP_ROUTES_CACHE = join(bootstrapCache, 'routes-v7.php');
        variables.APP_EVENTS_CACHE = join(bootstrapCache, 'events.php');
    }
    return variables;
}
function getDefaultPhpIniSettings() {
    return {
        'memory_limit': '512M',
        'curl.cainfo': state.caCert,
        'openssl.cafile': state.caCert
    };
}
function serveApp(secret, apiPort, phpIniSettings) {
    return new Promise((resolve, reject) => __awaiter(this, void 0, void 0, function* () {
        const appPath = getAppPath();
        console.log('Starting PHP server...', `${state.php} artisan serve`, appPath, phpIniSettings);
        ensureAppFoldersAreAvailable();
        console.log('Making sure app folders are available');
        const env = getDefaultEnvironmentVariables(secret, apiPort);
        const phpOptions = {
            cwd: appPath,
            env
        };
        const store = new Store({
            name: 'nativephp',
        });
        if (shouldOptimize(store)) {
            console.log('Caching view and routes...');
            let result = callPhpSync(['artisan', 'optimize'], phpOptions, phpIniSettings);
            if (result.status !== 0) {
                console.error('Failed to cache view and routes:', result.stderr.toString());
            }
            else {
                store.set('optimized_version', app.getVersion());
            }
        }
        if (shouldMigrateDatabase(store)) {
            console.log('Migrating database...');
            if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
                console.log('Database path:', databaseFile);
            }
            let result = callPhpSync(['artisan', 'migrate', '--force'], phpOptions, phpIniSettings);
            if (result.status !== 0) {
                console.error('Failed to migrate database:', result.stderr.toString());
            }
            else {
                store.set('migrated_version', app.getVersion());
            }
        }
        if (process.env.NODE_ENV === 'development') {
            console.log('Skipping Database migration while in development.');
            console.log('You may migrate manually by running: php artisan native:migrate');
        }
        console.log('Starting PHP server...');
        const phpPort = yield getPhpPort();
        let serverPath;
        let cwd;
        if (runningSecureBuild()) {
            serverPath = join(appPath, 'build', '__nativephp_app_bundle');
        }
        else {
            console.log('* * * Running from source * * *');
            serverPath = join(appPath, 'vendor', 'laravel', 'framework', 'src', 'Illuminate', 'Foundation', 'resources', 'server.php');
            cwd = join(appPath, 'public');
        }
        const phpServer = callPhp(['-S', `127.0.0.1:${phpPort}`, serverPath], {
            cwd: cwd,
            env
        }, phpIniSettings);
        const portRegex = /Development Server \(.*:([0-9]+)\) started/gm;
        phpServer.stdout.on('data', (data) => {
            if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
                console.log(data.toString().trim());
            }
        });
        phpServer.stderr.on('data', (data) => {
            const error = data.toString();
            const match = portRegex.exec(data.toString());
            if (match) {
                const port = parseInt(match[1]);
                console.log("PHP Server started on port: ", port);
                resolve({
                    port,
                    process: phpServer,
                });
            }
            else {
                if (error.includes('[NATIVE_EXCEPTION]:')) {
                    let logFile = join(storagePath, 'logs');
                    console.log();
                    console.error('Error in PHP:');
                    console.error('  ' + error.split('[NATIVE_EXCEPTION]:')[1].trim());
                    console.log('Please check your log files:');
                    console.log('  ' + logFile);
                    console.log();
                }
            }
        });
        phpServer.on('error', (error) => {
            reject(error);
        });
        phpServer.on('close', (code) => {
            console.log(`PHP server exited with code ${code}`);
        });
    }));
}
export { startScheduler, serveApp, getAppPath, retrieveNativePHPConfig, retrievePhpIniSettings, getDefaultEnvironmentVariables, getDefaultPhpIniSettings, runningSecureBuild };
