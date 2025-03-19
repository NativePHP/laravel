import express from 'express';
import {utilityProcess} from 'electron';
import state from '../state.js';
import {notifyLaravel} from "../utils.js";
import {getAppPath, getDefaultEnvironmentVariables, getDefaultPhpIniSettings, runningSecureBuild} from "../php.js";


import killSync from "kill-sync";
import {fileURLToPath} from "url";
import {join} from "path";

const router = express.Router();

function startProcess(settings) {
    const {alias, cmd, cwd, env, persistent, spawnTimeout = 30000} = settings;

    if (getProcess(alias) !== undefined) {
        return state.processes[alias];
    }

    try {
        const proc = utilityProcess.fork(
            fileURLToPath(new URL('../../electron-plugin/dist/server/childProcess.js', import.meta.url)),
            cmd,
            {
                cwd,
                stdio: 'pipe',
                serviceName: alias,
                env: {
                    ...process.env,
                    ...env,
                }
            }
        );

        // Set timeout to detect if process never spawns
        const startTimeout = setTimeout(() => {
            if (!state.processes[alias] || !state.processes[alias].pid) {
                console.error(`Process [${alias}] failed to start within timeout period`);

                // Attempt to clean up
                try {
                    proc.kill();
                } catch (e) {
                    // Ignore kill errors
                }

                notifyLaravel('events', {
                    event: 'Native\\Laravel\\Events\\ChildProcess\\StartupError',
                    payload: {
                        alias,
                        error: 'Startup timeout exceeded',
                    }
                });
            }
        }, spawnTimeout);

        proc.stdout.on('data', (data) => {
            notifyLaravel('events', {
                event: 'Native\\Laravel\\Events\\ChildProcess\\MessageReceived',
                payload: {
                    alias,
                    data: data.toString(),
                }
            });
        });

        proc.stderr.on('data', (data) => {
            console.error('Process [' + alias + '] ERROR:', data.toString().trim());

            notifyLaravel('events', {
                event: 'Native\\Laravel\\Events\\ChildProcess\\ErrorReceived',
                payload: {
                    alias,
                    data: data.toString(),
                }
            });
        });

        // Experimental feature on Electron,
        // I keep this here to remember and retry when we upgrade
        // https://www.electronjs.org/docs/latest/api/utility-process#event-error-experimental
        // proc.on('error', (error) => {
        //     clearTimeout(startTimeout);
        //     console.error(`Process [${alias}] error: ${error.message}`);
        //
        //     notifyLaravel('events', {
        //         event: 'Native\\Laravel\\Events\\ChildProcess\\StartupError',
        //         payload: {
        //             alias,
        //             error: error.toString(),
        //         }
        //     });
        // });

        proc.on('spawn', () => {
            clearTimeout(startTimeout);
            console.log('Process [' + alias + '] spawned!');

            state.processes[alias] = {
                pid: proc.pid,
                proc,
                settings
            };

            notifyLaravel('events', {
                event: 'Native\\Laravel\\Events\\ChildProcess\\ProcessSpawned',
                payload: [alias, proc.pid]
            });
        });

        proc.on('exit', (code) => {
            clearTimeout(startTimeout);
            console.log(`Process [${alias}] exited with code [${code}].`);

            notifyLaravel('events', {
                event: 'Native\\Laravel\\Events\\ChildProcess\\ProcessExited',
                payload: {
                    alias,
                    code,
                }
            });

            const settings = {...getSettings(alias)};
            delete state.processes[alias];

            if (settings?.persistent) {
                console.log('Process [' + alias + '] watchdog restarting...');
                // Add delay to prevent rapid restart loops
                setTimeout(() => startProcess(settings), 1000);
            }
        });

        return {
            pid: null,
            proc,
            settings
        };
    } catch (error) {
        console.error(`Failed to create process [${alias}]: ${error.message}`);

        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\ChildProcess\\StartupError',
            payload: {
                alias,
                error: error.toString(),
            }
        });

        return {
            pid: null,
            proc: null,
            settings,
            error: error.message
        };
    }
}

function startPhpProcess(settings) {
    const defaultEnv = getDefaultEnvironmentVariables(
        state.randomSecret,
        state.electronApiPort
    );

    // Construct command args from ini settings
    const customIniSettings = settings.iniSettings || {};
    const iniSettings = {...getDefaultPhpIniSettings(), ...state.phpIni, ...customIniSettings};
    const iniArgs = Object.keys(iniSettings).map(key => {
        return ['-d', `${key}=${iniSettings[key]}`];
    }).flat();

    if (settings.cmd[0] === 'artisan' && runningSecureBuild()) {
        settings.cmd.unshift(join(getAppPath(), 'build', '__nativephp_app_bundle'));
    }

    settings = {
        ...settings,
        // Prepend cmd with php executable path & ini settings
        cmd: [state.php, ...iniArgs, ...settings.cmd],
        // Mix in the internal NativePHP env
        env: {...settings.env, ...defaultEnv}
    };

    return startProcess(settings);
}

function stopProcess(alias) {
    const proc = getProcess(alias);

    if (proc === undefined) {
        return;
    }

    // Set persistent to false and prevent the process from restarting.
    state.processes[alias].settings.persistent = false;

    console.log('Process [' + alias + '] stopping with PID [' + proc.pid + '].');

    // @ts-ignore
    killSync(proc.pid, 'SIGTERM', true); // Kill tree
    proc.kill(); // Does not work but just in case. (do not put before killSync)
}

export function stopAllProcesses() {
    for (const alias in state.processes) {
        stopProcess(alias);
    }
}

function getProcess(alias) {
    return state.processes[alias]?.proc;
}

function getSettings(alias) {
    return state.processes[alias]?.settings;
}

router.post('/start', (req, res) => {
    const proc = startProcess(req.body);

    res.json(proc);
});

router.post('/start-php', (req, res) => {
    const proc = startPhpProcess(req.body);

    res.json(proc);
});

router.post('/stop', (req, res) => {
    const {alias} = req.body;

    stopProcess(alias);

    res.sendStatus(200);
});

router.post('/restart', async (req, res) => {
    const {alias} = req.body;

    const settings = {...getSettings(alias)};

    stopProcess(alias);

    if (settings === undefined) {
        res.sendStatus(410);
        return;
    }

    // Wait for the process to stop with a timeout of 5s
    const waitForProcessDeletion = async (timeout, retry) => {
        const start = Date.now();
        while (state.processes[alias] !== undefined) {
            if (Date.now() - start > timeout) {
                return;
            }
            await new Promise(resolve => setTimeout(resolve, retry));
        }
    };

    await waitForProcessDeletion(5000, 100);

    console.log('Process [' + alias + '] restarting...');
    const proc = startProcess(settings);
    res.json(proc);
});

router.get('/get/:alias', (req, res) => {
    const {alias} = req.params;

    const proc = state.processes[alias];

    if (proc === undefined) {
        res.sendStatus(410);
        return;
    }

    res.json(proc);
});

router.get('/', (req, res) => {
    res.json(state.processes);
});

router.post('/message', (req, res) => {
    const {alias, message} = req.body;

    const proc = getProcess(alias);

    if (proc === undefined) {
        res.sendStatus(200);
        return;
    }

    proc.postMessage(message);

    res.sendStatus(200);
});

export default router;
