import {mkdirSync, statSync, writeFileSync, existsSync} from 'fs'
import {copySync} from 'fs-extra'
import Store from 'electron-store'
import {promisify} from 'util'
import {join} from 'path'
import {app} from 'electron'
import {exec, execFile, spawn} from 'child_process'
import state from "./state";
import getPort from 'get-port';
import { ProcessResult } from "./ProcessResult";

const storagePath = join(app.getPath('userData'), 'storage')
const databasePath = join(app.getPath('userData'), 'database')
const databaseFile = join(databasePath, 'database.sqlite')
const argumentEnv = getArgumentEnv();
const appPath = getAppPath();

async function getPhpPort() {
    return await getPort({
        host: '127.0.0.1',
        port: getPort.makeRange(8100, 9000)
    });
}

async function retrievePhpIniSettings() {
  const env = {
    NATIVEPHP_RUNNING: 'true',
    NATIVEPHP_STORAGE_PATH: storagePath,
    NATIVEPHP_DATABASE_PATH: databaseFile,
  };

  const phpOptions = {
    cwd: appPath,
    env
  };

  return await promisify(execFile)(state.php, ['artisan', 'native:php-ini'], phpOptions);
}

async function retrieveNativePHPConfig() {
    const env = {
        NATIVEPHP_RUNNING: 'true',
        NATIVEPHP_STORAGE_PATH: storagePath,
        NATIVEPHP_DATABASE_PATH: databaseFile,
    };

    const phpOptions = {
        cwd: appPath,
        env
    };

    return await promisify(execFile)(state.php, ['artisan', 'native:config'], phpOptions);
}

function callPhp(args, options, phpIniSettings = {}) {

    let iniSettings = Object.assign(getDefaultPhpIniSettings(), phpIniSettings);

    Object.keys(iniSettings).forEach(key => {
      args.unshift('-d', `${key}=${iniSettings[key]}`);
    });

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

function getArgumentEnv() {
    const envArgs = process.argv.filter(arg => arg.startsWith('--env.'));

    const env: {
      TESTING?: number,
      APP_PATH?: string
    } = {

    };
    envArgs.forEach(arg => {
        const [key, value] = arg.slice(6).split('=');
        env[key] = value;
    });

    return env;
}

function getAppPath() {
    let appPath = join(__dirname, '../../resources/app/').replace('app.asar', 'app.asar.unpacked')

    if (process.env.NODE_ENV === 'development' || argumentEnv.TESTING == 1) {
        appPath = process.env.APP_PATH || argumentEnv.APP_PATH;
    }
    return appPath;
}

function ensureAppFoldersAreAvailable() {
    if (! existsSync(storagePath) || process.env.NODE_ENV === 'development') {
        copySync(join(appPath, 'storage'), storagePath)
    }

    mkdirSync(databasePath, {recursive: true})

    // Create a database file if it doesn't exist
    try {
        statSync(databaseFile)
    } catch (error) {
        writeFileSync(databaseFile, '')
    }
}

function startQueueWorker(secret, apiPort, phpIniSettings = {}) {
    const env = getDefaultEnvironmentVariables(secret, apiPort);

    const phpOptions = {
        cwd: appPath,
        env
    };

    return callPhp(['artisan', 'queue:work', '-q'], phpOptions, phpIniSettings);
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

function getDefaultEnvironmentVariables(secret, apiPort) {
  return {
    APP_ENV: process.env.NODE_ENV === 'development' ? 'local' : 'production',
    APP_DEBUG: process.env.NODE_ENV === 'development' ? 'true' : 'false',
    NATIVEPHP_STORAGE_PATH: storagePath,
    NATIVEPHP_DATABASE_PATH: databaseFile,
    NATIVEPHP_API_URL: `http://localhost:${apiPort}/api/`,
    NATIVEPHP_RUNNING: 'true',
    NATIVEPHP_SECRET: secret,
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

        const store = new Store();

        // Make sure the storage path is linked - as people can move the app around, we
        // need to run this every time the app starts
        callPhp(['artisan', 'storage:link', '--force'], phpOptions, phpIniSettings)

        // Migrate the database
        if (store.get('migrated_version') !== app.getVersion() && process.env.NODE_ENV !== 'development') {
            console.log('Migrating database...')
            callPhp(['artisan', 'migrate', '--force'], phpOptions, phpIniSettings)
            store.set('migrated_version', app.getVersion())
        }

        if (process.env.NODE_ENV === 'development') {
            console.log('Skipping Database migration while in development.')
            console.log('You may migrate manually by running: php artisan native:migrate')
        }

        const phpPort = await getPhpPort();

        const serverPath = join(appPath, 'vendor', 'laravel', 'framework', 'src', 'Illuminate', 'Foundation', 'resources', 'server.php')
        const phpServer = callPhp(['-S', `127.0.0.1:${phpPort}`, serverPath], {
            cwd: join(appPath, 'public'),
            env
        }, phpIniSettings)

        const portRegex = /Development Server \(.*:([0-9]+)\) started/gm

        phpServer.stdout.on('data', (data) => {
            const match = portRegex.exec(data.toString())
            if (match) {
                console.log("PHP Server started on port: ", match[1])
                const port = parseInt(match[1])
                resolve({
                    port,
                    process: phpServer
                })
            }
        })

        phpServer.stderr.on('data', (data) => {
            const error = data.toString();
            const match = portRegex.exec(error);

            if (match) {
                const port = parseInt(match[1]);
                console.log("PHP Server started on port: ", port);
                resolve({
                    port,
                    process: phpServer
                });
            } else {
                // 27 is the length of the php -S output preamble
                if (error.startsWith('[NATIVE_EXCEPTION]: ', 27)) {
                    console.log();
                    console.error('Error in PHP:');
                    console.error('  ' + error.slice(47));
                    console.log('Please check your log file:');
                    console.log('  ' + join(appPath, 'storage', 'logs', 'laravel.log'));
                    console.log();
                }
            }
        });

        phpServer.on('error', (error) => {
            reject(error)
        })
    })
}

export {startQueueWorker, startScheduler, serveApp, getAppPath, retrieveNativePHPConfig, retrievePhpIniSettings, getDefaultEnvironmentVariables, getDefaultPhpIniSettings}
