var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import startAPIServer from "./api";
import { retrieveNativePHPConfig, retrievePhpIniSettings, serveApp, startQueueWorker, startScheduler, } from "./php";
import { appendCookie } from "./utils";
import state from "./state";
export function startPhpApp() {
    return __awaiter(this, void 0, void 0, function* () {
        const result = yield serveApp(state.randomSecret, state.electronApiPort, state.phpIni);
        state.phpPort = result.port;
        yield appendCookie();
        return result.process;
    });
}
export function startQueue() {
    if (!process.env.NATIVE_PHP_SKIP_QUEUE) {
        return startQueueWorker(state.randomSecret, state.electronApiPort, state.phpIni);
    }
    else {
        return undefined;
    }
}
export function runScheduler() {
    startScheduler(state.randomSecret, state.electronApiPort, state.phpIni);
}
export function startAPI() {
    return startAPIServer(state.randomSecret);
}
export { retrieveNativePHPConfig, retrievePhpIniSettings };
