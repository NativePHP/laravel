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
import state from '../state';
import { notifyLaravel } from "../utils";
import { join } from 'path';
import { getDefaultEnvironmentVariables, getDefaultPhpIniSettings } from "../php";
const router = express.Router();
const killSync = require('kill-sync');
function startProcess(settings) {
    const { alias, cmd, cwd, env, persistent } = settings;
    if (getProcess(alias) !== undefined) {
        return state.processes[alias];
    }
    const proc = utilityProcess.fork(join(__dirname, '../../electron-plugin/dist/server/childProcess.js'), cmd, {
        cwd,
        stdio: 'pipe',
        serviceName: alias,
        env: Object.assign(Object.assign({}, process.env), env)
    });
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
        console.error('Error received from process [' + alias + ']:', data.toString());
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\ChildProcess\\ErrorReceived',
            payload: {
                alias,
                data: data.toString(),
            }
        });
    });
    proc.on('spawn', () => {
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
        if (settings.persistent) {
            console.log('Process [' + alias + '] watchdog restarting...');
            startProcess(settings);
        }
    });
    return {
        pid: null,
        proc,
        settings
    };
}
function startPhpProcess(settings) {
    const defaultEnv = getDefaultEnvironmentVariables(state.randomSecret, state.electronApiPort);
    const iniSettings = Object.assign(Object.assign({}, getDefaultPhpIniSettings()), state.phpIni);
    const iniArgs = Object.keys(iniSettings).map(key => {
        return ['-d', `${key}=${iniSettings[key]}`];
    }).flat();
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
