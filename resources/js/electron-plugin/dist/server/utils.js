var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { session } from 'electron';
import state from './state';
import axios from 'axios';
export function appendCookie() {
    return __awaiter(this, void 0, void 0, function* () {
        const cookie = {
            url: `http://localhost:${state.phpPort}`,
            name: "_php_native",
            value: state.randomSecret,
        };
        yield session.defaultSession.cookies.set(cookie);
    });
}
export function notifyLaravel(endpoint, payload = {}) {
    return __awaiter(this, void 0, void 0, function* () {
        if (endpoint === 'events') {
            broadcastToWindows('native-event', payload);
        }
        try {
            yield axios.post(`http://127.0.0.1:${state.phpPort}/_native/api/${endpoint}`, payload, {
                headers: {
                    "X-NativePHP-Secret": state.randomSecret,
                },
            });
        }
        catch (e) {
        }
    });
}
export function broadcastToWindows(event, payload) {
    var _a;
    Object.values(state.windows).forEach(window => {
        window.webContents.send(event, payload);
    });
    if ((_a = state.activeMenuBar) === null || _a === void 0 ? void 0 : _a.window) {
        state.activeMenuBar.window.webContents.send(event, payload);
    }
}
export function trimOptions(options) {
    Object.keys(options).forEach(key => options[key] == null && delete options[key]);
    return options;
}
export function appendWindowIdToUrl(url, id) {
    return url + (url.indexOf('?') === -1 ? '?' : '&') + '_windowId=' + id;
}
export function goToUrl(url, windowId) {
    var _a;
    (_a = state.windows[windowId]) === null || _a === void 0 ? void 0 : _a.loadURL(appendWindowIdToUrl(url, windowId));
}
