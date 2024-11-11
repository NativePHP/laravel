var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { app } from "electron";
import { autoUpdater } from "electron-updater";
import state from "./server/state";
import { electronApp, optimizer } from "@electron-toolkit/utils";
import { retrieveNativePHPConfig, retrievePhpIniSettings, runScheduler, startAPI, startPhpApp, startQueue, } from "./server";
import { notifyLaravel } from "./server/utils";
import { resolve } from "path";
import { stopAllProcesses } from "./server/api/childProcess";
import ps from "ps-node";
class NativePHP {
    constructor() {
        this.processes = [];
        this.schedulerInterval = undefined;
    }
    bootstrap(app, icon, phpBinary, cert) {
        require("@electron/remote/main").initialize();
        state.icon = icon;
        state.php = phpBinary;
        state.caCert = cert;
        this.bootstrapApp(app);
        this.addEventListeners(app);
    }
    addEventListeners(app) {
        app.on("open-url", (event, url) => {
            notifyLaravel("events", {
                event: "\\Native\\Laravel\\Events\\App\\OpenedFromURL",
                payload: [url],
            });
        });
        app.on("open-file", (event, path) => {
            notifyLaravel("events", {
                event: "\\Native\\Laravel\\Events\\App\\OpenFile",
                payload: [path],
            });
        });
        app.on("window-all-closed", () => {
            if (process.platform !== "darwin") {
                app.quit();
            }
        });
        app.on("before-quit", () => {
            if (this.schedulerInterval) {
                clearInterval(this.schedulerInterval);
            }
            stopAllProcesses();
            this.killChildProcesses();
        });
        app.on("browser-window-created", (_, window) => {
            optimizer.watchWindowShortcuts(window);
        });
        app.on("activate", function (event, hasVisibleWindows) {
            if (!hasVisibleWindows) {
                notifyLaravel("booted");
            }
            event.preventDefault();
        });
    }
    bootstrapApp(app) {
        return __awaiter(this, void 0, void 0, function* () {
            yield app.whenReady();
            const config = yield this.loadConfig();
            this.setDockIcon();
            this.setAppUserModelId(config);
            this.setDeepLinkHandler(config);
            this.startAutoUpdater(config);
            yield this.startElectronApi();
            state.phpIni = yield this.loadPhpIni();
            yield this.startPhpApp();
            yield this.startQueueWorker();
            this.startScheduler();
            yield notifyLaravel("booted");
        });
    }
    loadConfig() {
        return __awaiter(this, void 0, void 0, function* () {
            let config = {};
            try {
                const result = yield retrieveNativePHPConfig();
                config = JSON.parse(result.stdout);
            }
            catch (error) {
                console.error(error);
            }
            return config;
        });
    }
    setDockIcon() {
        if (process.platform === "darwin" &&
            process.env.NODE_ENV === "development") {
            app.dock.setIcon(state.icon);
        }
    }
    setAppUserModelId(config) {
        electronApp.setAppUserModelId(config === null || config === void 0 ? void 0 : config.app_id);
    }
    setDeepLinkHandler(config) {
        const deepLinkProtocol = config === null || config === void 0 ? void 0 : config.deeplink_scheme;
        if (deepLinkProtocol) {
            if (process.defaultApp) {
                if (process.argv.length >= 2) {
                    app.setAsDefaultProtocolClient(deepLinkProtocol, process.execPath, [
                        resolve(process.argv[1]),
                    ]);
                }
            }
            else {
                app.setAsDefaultProtocolClient(deepLinkProtocol);
            }
        }
    }
    startAutoUpdater(config) {
        var _a;
        if (((_a = config === null || config === void 0 ? void 0 : config.updater) === null || _a === void 0 ? void 0 : _a.enabled) === true) {
            autoUpdater.checkForUpdatesAndNotify();
        }
    }
    startElectronApi() {
        return __awaiter(this, void 0, void 0, function* () {
            const electronApi = yield startAPI();
            state.electronApiPort = electronApi.port;
            console.log("Electron API server started on port", electronApi.port);
        });
    }
    loadPhpIni() {
        return __awaiter(this, void 0, void 0, function* () {
            let config = {};
            try {
                const result = yield retrievePhpIniSettings();
                config = JSON.parse(result.stdout);
            }
            catch (error) {
                console.error(error);
            }
            return config;
        });
    }
    startPhpApp() {
        return __awaiter(this, void 0, void 0, function* () {
            this.processes.push(yield startPhpApp());
        });
    }
    startQueueWorker() {
        return __awaiter(this, void 0, void 0, function* () {
            this.processes.push(yield startQueue());
        });
    }
    startScheduler() {
        const now = new Date();
        const delay = (60 - now.getSeconds()) * 1000 + (1000 - now.getMilliseconds());
        setTimeout(() => {
            console.log("Running scheduler...");
            runScheduler();
            this.schedulerInterval = setInterval(() => {
                console.log("Running scheduler...");
                runScheduler();
            }, 60 * 1000);
        }, delay);
    }
    killChildProcesses() {
        this.processes
            .filter((p) => p !== undefined)
            .forEach((process) => {
            try {
                ps.kill(process.pid);
            }
            catch (err) {
                console.error(err);
            }
        });
    }
}
export default new NativePHP();
