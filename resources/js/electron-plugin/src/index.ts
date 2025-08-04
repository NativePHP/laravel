import CrossProcessExports from "electron";
import { app, session, powerMonitor } from "electron";
import { initialize } from "@electron/remote/main/index.js";
import state from "./server/state.js";
import { electronApp, optimizer } from "@electron-toolkit/utils";
import {
  retrieveNativePHPConfig,
  retrievePhpIniSettings,
  runScheduler,
  startAPI,
  startPhpApp,
} from "./server/index.js";
import { notifyLaravel } from "./server/utils.js";
import { resolve } from "path";
import { stopAllProcesses } from "./server/api/childProcess.js";
import ps from "ps-node";
import killSync from "kill-sync";

// Workaround for CommonJS module
import electronUpdater from 'electron-updater';
const { autoUpdater } = electronUpdater;

class NativePHP {
  processes = [];
  schedulerInterval = undefined;
  mainWindow = null;

  public bootstrap(
    app: CrossProcessExports.App,
    icon: string,
    phpBinary: string,
    cert: string
  ) {

    initialize();

    state.icon = icon;
    state.php = phpBinary;
    state.caCert = cert;

    this.bootstrapApp(app);
    this.addEventListeners(app);
  }

  private addEventListeners(app: Electron.CrossProcessExports.App) {
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

      // close all child processes from the app
      stopAllProcesses();

      this.killChildProcesses();
    });

    // Default open or close DevTools by F12 in development
    // and ignore CommandOrControl + R in production.
    // see https://github.com/alex8088/electron-toolkit/tree/master/packages/utils
    app.on("browser-window-created", (_, window) => {
      optimizer.watchWindowShortcuts(window);
    });

    app.on("activate", function (event, hasVisibleWindows) {
      // On macOS it's common to re-create a window in the app when the
      // dock icon is clicked and there are no other windows open.
      if (!hasVisibleWindows) {
        notifyLaravel("booted");
      }

      event.preventDefault();
    });
  }

  private async bootstrapApp(app: Electron.CrossProcessExports.App) {
    await app.whenReady();

    const config = await this.loadConfig();

    this.setDockIcon();
    this.setAppUserModelId(config);
    this.setDeepLinkHandler(config);
    this.startAutoUpdater(config);

    await this.startElectronApi();

    state.phpIni = await this.loadPhpIni();

    await this.startPhpApp();
    this.startScheduler();

    powerMonitor.on("suspend", () => {
      this.stopScheduler();
    });

    powerMonitor.on("resume", () => {
      this.stopScheduler();
      this.startScheduler();
    });

    const filter = {
        urls: [`http://127.0.0.1:${state.phpPort}/*`]
    };

    session.defaultSession.webRequest.onBeforeSendHeaders(filter, (details, callback) => {
        details.requestHeaders['X-NativePHP-Secret'] = state.randomSecret;

        callback({ requestHeaders: details.requestHeaders });
    });

    await notifyLaravel("booted");
  }

  private async loadConfig() {
    let config = {};

    try {
      const result = await retrieveNativePHPConfig();

      config = JSON.parse(result.stdout);
    } catch (error) {
      console.error(error);
    }

    return config;
  }

  private setDockIcon() {
    // Only run this on macOS
    if (
      process.platform === "darwin" &&
      process.env.NODE_ENV === "development"
    ) {
      app.dock.setIcon(state.icon);
    }
  }

  private setAppUserModelId(config) {
    electronApp.setAppUserModelId(config?.app_id);
  }

  private setDeepLinkHandler(config) {
    const deepLinkProtocol = config?.deeplink_scheme;

    if (deepLinkProtocol) {
      if (process.defaultApp) {
        if (process.argv.length >= 2) {
          app.setAsDefaultProtocolClient(deepLinkProtocol, process.execPath, [
            resolve(process.argv[1]),
          ]);
        }
      } else {
        app.setAsDefaultProtocolClient(deepLinkProtocol);
      }

            /**
             * Handle protocol url for windows and linux
             * This code will be different in Windows and Linux compared to MacOS.
             * This is due to both platforms emitting the second-instance event rather
             * than the open-url event and Windows requiring additional code in order to
             * open the contents of the protocol link within the same Electron instance.
             */
            if (process.platform !== "darwin") {
                const gotTheLock = app.requestSingleInstanceLock();
                if (!gotTheLock) {
                    app.quit();
                    return;
                } else {
                    app.on(
                        "second-instance",
                        (event, commandLine, workingDirectory) => {
                            // Someone tried to run a second instance, we should focus our window.
                            if (this.mainWindow) {
                                if (this.mainWindow.isMinimized())
                                    this.mainWindow.restore();
                                this.mainWindow.focus();
                            }

                            // the commandLine is array of strings in which last element is deep link url
                            notifyLaravel("events", {
                                event: "\\Native\\Laravel\\Events\\App\\OpenedFromURL",
                                payload: {
                                    url: commandLine[commandLine.length - 1],
                                },
                            });
                        },
                    );
                }
            }
        }
    }

  private startAutoUpdater(config) {
    if (config?.updater?.enabled === true) {
      autoUpdater.checkForUpdatesAndNotify();
    }
  }

  private async startElectronApi() {
    // Start an Express server so that the Electron app can be controlled from PHP via API
    const electronApi = await startAPI();

    state.electronApiPort = electronApi.port;

    console.log("Electron API server started on port", electronApi.port);
  }

  private async loadPhpIni() {
    let config = {};

    try {
      const result = await retrievePhpIniSettings();

      config = JSON.parse(result.stdout);
    } catch (error) {
      console.error(error);
    }

    return config;
  }

  private async startPhpApp() {
    this.processes.push(await startPhpApp());
  }


    private stopScheduler() {
        if (this.schedulerInterval) {
            clearInterval(this.schedulerInterval);
            this.schedulerInterval = null;
        }
    }

  private startScheduler() {
    const now = new Date();
    const delay =
      (60 - now.getSeconds()) * 1000 + (1000 - now.getMilliseconds());

    setTimeout(() => {
      console.log("Running scheduler...");

      runScheduler();

      this.schedulerInterval = setInterval(() => {
        console.log("Running scheduler...");

        runScheduler();
      }, 60 * 1000);
    }, delay);
  }

  private killChildProcesses() {
    this.processes
      .filter((p) => p !== undefined)
      .forEach((process) => {
        try {
          // @ts-ignore
          killSync(process.pid, 'SIGTERM', true); // Kill tree
          ps.kill(process.pid); // Sometimes does not kill the subprocess of php server
        } catch (err) {
          console.error(err);
        }
      });
  }
}

export default new NativePHP();
