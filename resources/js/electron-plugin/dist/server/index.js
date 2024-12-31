var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import startAPIServer from "./api.js";
import { retrieveNativePHPConfig, retrievePhpIniSettings, serveApp, startScheduler, } from "./php.js";
import { appendCookie } from "./utils.js";
import state from "./state.js";
export function startPhpApp() {
    return __awaiter(this, void 0, void 0, function* () {
        const result = yield serveApp(state.randomSecret, state.electronApiPort, state.phpIni);
        state.phpPort = result.port;
        yield appendCookie();
        return result.process;
    });
}
export function runScheduler() {
    startScheduler(state.randomSecret, state.electronApiPort, state.phpIni);
}
export function startAPI() {
    return startAPIServer(state.randomSecret);
}
export { retrieveNativePHPConfig, retrievePhpIniSettings };
