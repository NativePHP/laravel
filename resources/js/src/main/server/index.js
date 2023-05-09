import {session} from 'electron'
import serveWebsockets from './websockets'
import startAPIServer from './api'
import {startQueueWorker, startScheduler, serveApp} from './php';
import axios from 'axios';

let phpPort = null;
const randomSecret = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);

export async function servePhpApp(apiPort) {
    let processses = [];
    const result = await serveApp(randomSecret, apiPort);
    processses.push(result.process);

    processses.push(startQueueWorker(randomSecret, apiPort));

    phpPort = result.port;
    appendCookie();

    return processses;
}

export function runScheduler(apiPort) {
    startScheduler(randomSecret, apiPort);
}

export function startQueue(apiPort) {
    if (! process.env.NATIVE_PHP_SKIP_QUEUE) {
        return startQueueWorker(randomSecret, apiPort);
    }
}

export function startAPI() {
    return startAPIServer(randomSecret);
}

export {serveWebsockets}

export async function appendCookie() {
    const cookie = {url: `http://localhost:${phpPort}`, name: '_php_native', value: randomSecret}
    await session.defaultSession.cookies.set(cookie)
}

export async function notifyLaravel(endpoint, payload = {}) {
    try {
        await axios.post(`http://localhost:${phpPort}/_native/api/${endpoint}`, payload, {
            headers: {
                'X-Native-PHP-Secret': randomSecret
            }
        })
    } catch (e) {
    }
}
