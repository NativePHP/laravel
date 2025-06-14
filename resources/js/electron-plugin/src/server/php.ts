import {mkdirSync, statSync, writeFileSync, existsSync} from 'fs'
import fs_extra from 'fs-extra';

const {copySync, mkdirpSync} = fs_extra;

import Store from 'electron-store'
import {promisify} from 'util'
import {join} from 'path'
import {app} from 'electron'
import {execFile, spawn, spawnSync} from 'child_process'
import state from "./state.js";
import getPort, {portNumbers} from 'get-port';
import {ProcessResult} from "./ProcessResult.js";

// TODO: maybe in dev, don't go to the userData folder and stay in the Laravel app folder
const storagePath = join(app.getPath('userData'), 'storage')
const databasePath = join(app.getPath('userData'), 'database')
const databaseFile = join(databasePath, 'database.sqlite')
const bootstrapCache = join(app.getPath('userData'), 'bootstrap', 'cache')
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
    /*
     * For some weird reason,
     * the cached config is not picked up on subsequent launches,
     * so we'll just rebuilt it every time for now
     */

    return process.env.NODE_ENV !== 'development';
    // return runningSecureBuild();
    // return runningSecureBuild() && store.get('optimized_version') !== app.getVersion();
}

async function getPhpPort() {
    return await getPort({
        host: '127.0.0.1',
        port: portNumbers(8100, 9000)
    });
}

async function retrievePhpIniSettings() {
    const env = getDefaultEnvironmentVariables() as any;

    const phpOptions = {
        cwd: appPath,
        env
    };

    let command = ['artisan', 'native:php-ini'];

    if (runningSecureBuild()) {
        command.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }

    return await promisify(execFile)(state.php, command, phpOptions);
}

async function retrieveNativePHPConfig() {
    const env = getDefaultEnvironmentVariables() as any;

    const phpOptions = {
        cwd: appPath,
        env
    };

    let command = ['artisan', 'native:config'];

    if (runningSecureBuild()) {
        command.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }

    return await promisify(execFile)(state.php, command, phpOptions);
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

    return spawn(
        state.php,
        args,
        {
            cwd: options.cwd,
            env: {
                ...process.env,
                ...options.env
            },
        }
    );
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

    return spawnSync(
        state.php,
        args,
        {
            cwd: options.cwd,
            env: {
                ...process.env,
                ...options.env
            }
        }
    );
}

function getArgumentEnv() {
    const envArgs = process.argv.filter(arg => arg.startsWith('--env.'));

    const env: {
        TESTING?: number,
        APP_PATH?: string
    } = {};
    envArgs.forEach(arg => {
        const [key, value] = arg.slice(6).split('=');
        env[key] = value;
    });

    return env;
}

function getAppPath() {
    let appPath = join(import.meta.dirname, '../../resources/app/').replace('app.asar', 'app.asar.unpacked')

    if (process.env.NODE_ENV === 'development' || argumentEnv.TESTING == 1) {
        appPath = process.env.APP_PATH || argumentEnv.APP_PATH;
    }
    return appPath;
}

function ensureAppFoldersAreAvailable() {

    // if (!runningSecureBuild()) {
    console.log('Copying storage folder...');
    console.log('Storage path:', storagePath);
        if (!existsSync(storagePath) || process.env.NODE_ENV === 'development') {
            console.log("App path:", appPath);
            copySync(join(appPath, 'storage'), storagePath)
        }
    // }

    mkdirSync(databasePath, {recursive: true})

    // Create a database file if it doesn't exist
    try {
        statSync(databaseFile)
    } catch (error) {
        writeFileSync(databaseFile, '')
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

function getPath(name: string) {
    try {
        // @ts-ignore
        return app.getPath(name);
    } catch (error) {
        return '';
    }
}

// Define an interface for the environment variables
interface EnvironmentVariables {
    APP_ENV: string;
    APP_DEBUG: string;
    LARAVEL_STORAGE_PATH: string;
    NATIVEPHP_STORAGE_PATH: string;
    NATIVEPHP_DATABASE_PATH: string;
    NATIVEPHP_API_URL?: string;
    NATIVEPHP_RUNNING: string;
    NATIVEPHP_SECRET?: string;
    NATIVEPHP_USER_HOME_PATH: string;
    NATIVEPHP_APP_DATA_PATH: string;
    NATIVEPHP_USER_DATA_PATH: string;
    NATIVEPHP_DESKTOP_PATH: string;
    NATIVEPHP_DOCUMENTS_PATH: string;
    NATIVEPHP_DOWNLOADS_PATH: string;
    NATIVEPHP_MUSIC_PATH: string;
    NATIVEPHP_PICTURES_PATH: string;
    NATIVEPHP_VIDEOS_PATH: string;
    NATIVEPHP_RECENT_PATH: string;
    // Cache variables
    APP_SERVICES_CACHE?: string;
    APP_PACKAGES_CACHE?: string;
    APP_CONFIG_CACHE?: string;
    APP_ROUTES_CACHE?: string;
    APP_EVENTS_CACHE?: string;
    VIEW_COMPILED_PATH?: string;
}

function getDefaultEnvironmentVariables(secret?: string, apiPort?: number): EnvironmentVariables {
    // Base variables with string values (no null values)
    let variables: EnvironmentVariables = {
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

    // Only if the server has already started
    if (secret && apiPort) {
        variables.NATIVEPHP_API_URL = `http://localhost:${apiPort}/api/`;
        variables.NATIVEPHP_SECRET = secret;
    }

    // Only add cache paths if in production mode
    if (runningSecureBuild()) {
        variables.APP_SERVICES_CACHE = join(bootstrapCache, 'services.php'); // Should be present and writable
        variables.APP_PACKAGES_CACHE = join(bootstrapCache, 'packages.php'); // Should be present and writable
        variables.APP_CONFIG_CACHE = join(bootstrapCache, 'config.php');
        variables.APP_ROUTES_CACHE = join(bootstrapCache, 'routes-v7.php');
        variables.APP_EVENTS_CACHE = join(bootstrapCache, 'events.php');
        // variables.VIEW_COMPILED_PATH; // TODO: keep those in the phar file if we can.
    }

    return variables;
}

function getDefaultPhpIniSettings() {
    return {
        'memory_limit': '512M',
        'curl.cainfo': state.caCert,
        'openssl.cafile': state.caCert
    }
}

function serveApp(secret, apiPort, phpIniSettings): Promise<ProcessResult> {
    return new Promise(async (resolve, reject) => {
        const appPath = getAppPath();

        console.log('Starting PHP server...', `${state.php} artisan serve`, appPath, phpIniSettings)

        ensureAppFoldersAreAvailable();

        console.log('Making sure app folders are available')

        const env = getDefaultEnvironmentVariables(secret, apiPort);

        const phpOptions = {
            cwd: appPath,
            env
        };

        const store = new Store({
            name: 'nativephp', // So it doesn't conflict with settings of the app
        });

        // Make sure the storage path is linked - as people can move the app around, we
        // need to run this every time the app starts
        if (!runningSecureBuild()) {
            /*
              * Simon: Note for later that we should strip out using storage:link
              * all of the necessary files for the app to function should be a part of the bundle
              * (whether it's a secured bundle or not), so symlinking feels redundant
             */
            console.log('Linking storage path...');
            callPhp(['artisan', 'storage:link', '--force'], phpOptions, phpIniSettings)
        }

        // Cache the project
        if (shouldOptimize(store)) {
            console.log('Caching view and routes...');

            let result = callPhpSync(['artisan', 'optimize'], phpOptions, phpIniSettings);

            if (result.status !== 0) {
                console.error('Failed to cache view and routes:', result.stderr.toString());
            } else {
                store.set('optimized_version', app.getVersion())
            }
        }

        // Migrate the database
        if (shouldMigrateDatabase(store)) {
            console.log('Migrating database...');

            if(parseInt(process.env.SHELL_VERBOSITY) > 0) {
                console.log('Database path:', databaseFile);
            }

            let result = callPhpSync(['artisan', 'migrate', '--force'], phpOptions, phpIniSettings);

            if (result.status !== 0) {
                console.error('Failed to migrate database:', result.stderr.toString());
            } else {
                store.set('migrated_version', app.getVersion())
            }
        }

        if (process.env.NODE_ENV === 'development') {
            console.log('Skipping Database migration while in development.')
            console.log('You may migrate manually by running: php artisan native:migrate')
        }

        console.log('Starting PHP server...');
        const phpPort = await getPhpPort();


        let serverPath: string;
        let cwd: string;

        if (runningSecureBuild()) {
            serverPath = join(appPath, 'build', '__nativephp_app_bundle');
        } else {
            console.log('* * * Running from source * * *');
            serverPath = join(appPath, 'vendor', 'laravel', 'framework', 'src', 'Illuminate', 'Foundation', 'resources', 'server.php');
            cwd = join(appPath, 'public');
        }

        const phpServer = callPhp(['-S', `127.0.0.1:${phpPort}`, serverPath], {
            cwd: cwd,
            env
        }, phpIniSettings)

        const portRegex = /Development Server \(.*:([0-9]+)\) started/gm

        // Show urls called
        phpServer.stdout.on('data', (data) => {
            // [Tue Jan 14 19:51:00 2025] 127.0.0.1:52779 [POST] URI: /_native/api/events

            if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
                console.log(data.toString().trim());
            }
        })

        // Show PHP errors and indicate which port the server is running on
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
            } else {
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

        // Log when any error occurs (not started, not killed, couldn't send message, etc)
        phpServer.on('error', (error) => {
            reject(error)
        });

        // Log when the PHP server exits
        phpServer.on('close', (code) => {
            console.log(`PHP server exited with code ${code}`);
        });
    })
}

export {
    startScheduler,
    serveApp,
    getAppPath,
    retrieveNativePHPConfig,
    retrievePhpIniSettings,
    getDefaultEnvironmentVariables,
    getDefaultPhpIniSettings,
    runningSecureBuild
}
