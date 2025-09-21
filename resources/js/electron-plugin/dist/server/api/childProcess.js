var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import express from 'express';
import { utilityProcess } from 'electron';
import state from '../state.js';
import { notifyLaravel } from "../utils.js";
import { getAppPath, getDefaultEnvironmentVariables, getDefaultPhpIniSettings, runningSecureBuild } from "../php.js";
import killSync from "kill-sync";
import { fileURLToPath } from "url";
import { join } from "path";
const router = express.Router();
function startProcess(settings) {
    const { alias, cmd, cwd, env, persistent, spawnTimeout = 30000 } = settings;
    if (getProcess(alias) !== undefined) {
        return state.processes[alias];
    }
    try {
        const proc = utilityProcess.fork(fileURLToPath(new URL('../../electron-plugin/dist/server/childProcess.js', import.meta.url)), cmd, {
            cwd,
            stdio: 'pipe',
            serviceName: alias,
            env: Object.assign(Object.assign({}, process.env), env)
        });
        const startTimeout = setTimeout(() => {
            if (!state.processes[alias] || !state.processes[alias].pid) {
                console.error(`Process [${alias}] failed to start within timeout period`);
                try {
                    proc.kill();
                }
                catch (e) {
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
            const settings = Object.assign({}, getSettings(alias));
            delete state.processes[alias];
            if (settings === null || settings === void 0 ? void 0 : settings.persistent) {
                console.log('Process [' + alias + '] watchdog restarting...');
                setTimeout(() => startProcess(settings), 1000);
            }
        });
        return {
            pid: null,
            proc,
            settings
        };
    }
    catch (error) {
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
    const defaultEnv = getDefaultEnvironmentVariables(state.randomSecret, state.electronApiPort);
    const customIniSettings = settings.iniSettings || {};
    const iniSettings = Object.assign(Object.assign(Object.assign({}, getDefaultPhpIniSettings()), state.phpIni), customIniSettings);
    const iniArgs = Object.keys(iniSettings).map(key => {
        return ['-d', `${key}=${iniSettings[key]}`];
    }).flat();
    if (settings.cmd[0] === 'artisan' && runningSecureBuild()) {
        settings.cmd.unshift(join(getAppPath(), 'build', '__nativephp_app_bundle'));
    }
    settings = Object.assign(Object.assign({}, settings), { cmd: [state.php, ...iniArgs, ...settings.cmd], env: Object.assign(Object.assign({}, settings.env), defaultEnv) });
    return startProcess(settings);
}
function stopProcess(alias) {
    const proc = getProcess(alias);
    if (proc === undefined) {
        return;
    }
    state.processes[alias].settings.persistent = false;
    console.log('Process [' + alias + '] stopping with PID [' + proc.pid + '].');
    killSync(proc.pid, 'SIGTERM', true);
    proc.kill();
}
export function stopAllProcesses() {
    for (const alias in state.processes) {
        stopProcess(alias);
    }
}
function getProcess(alias) {
    var _a;
    return (_a = state.processes[alias]) === null || _a === void 0 ? void 0 : _a.proc;
}
function getSettings(alias) {
    var _a;
    return (_a = state.processes[alias]) === null || _a === void 0 ? void 0 : _a.settings;
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
    const { alias } = req.body;
    stopProcess(alias);
    res.sendStatus(200);
});
router.post('/restart', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { alias } = req.body;
    const settings = Object.assign({}, getSettings(alias));
    stopProcess(alias);
    if (settings === undefined) {
        res.sendStatus(410);
        return;
    }
    const waitForProcessDeletion = (timeout, retry) => __awaiter(void 0, void 0, void 0, function* () {
        const start = Date.now();
        while (state.processes[alias] !== undefined) {
            if (Date.now() - start > timeout) {
                return;
            }
            yield new Promise(resolve => setTimeout(resolve, retry));
        }
    });
    yield waitForProcessDeletion(5000, 100);
    console.log('Process [' + alias + '] restarting...');
    const proc = startProcess(settings);
    res.json(proc);
}));
router.get('/get/:alias', (req, res) => {
    const { alias } = req.params;
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
    const { alias, message } = req.body;
    const proc = getProcess(alias);
    if (proc === undefined) {
        res.sendStatus(200);
        return;
    }
    proc.postMessage(message);
    res.sendStatus(200);
});
export default router;
