var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import express from "express";
import bodyParser from "body-parser";
import getPort, { portNumbers } from "get-port";
import middleware from "./api/middleware.js";
import clipboardRoutes from "./api/clipboard.js";
import alertRoutes from "./api/alert.js";
import appRoutes from "./api/app.js";
import autoUpdaterRoutes from "./api/autoUpdater.js";
import screenRoutes from "./api/screen.js";
import dialogRoutes from "./api/dialog.js";
import debugRoutes from "./api/debug.js";
import broadcastingRoutes from "./api/broadcasting.js";
import systemRoutes from "./api/system.js";
import globalShortcutRoutes from "./api/globalShortcut.js";
import notificationRoutes from "./api/notification.js";
import dockRoutes from "./api/dock.js";
import menuRoutes from "./api/menu.js";
import menuBarRoutes from "./api/menuBar.js";
import windowRoutes from "./api/window.js";
import processRoutes from "./api/process.js";
import contextMenuRoutes from "./api/contextMenu.js";
import settingsRoutes from "./api/settings.js";
import shellRoutes from "./api/shell.js";
import progressBarRoutes from "./api/progressBar.js";
import powerMonitorRoutes from "./api/powerMonitor.js";
import childProcessRoutes from "./api/childProcess.js";
function startAPIServer(randomSecret) {
    return __awaiter(this, void 0, void 0, function* () {
        const port = yield getPort({
            port: portNumbers(4000, 5000),
        });
        return new Promise((resolve, reject) => {
            const httpServer = express();
            httpServer.use(middleware(randomSecret));
            httpServer.use(bodyParser.json());
            httpServer.use("/api/clipboard", clipboardRoutes);
            httpServer.use("/api/alert", alertRoutes);
            httpServer.use("/api/app", appRoutes);
            httpServer.use("/api/auto-updater", autoUpdaterRoutes);
            httpServer.use("/api/screen", screenRoutes);
            httpServer.use("/api/dialog", dialogRoutes);
            httpServer.use("/api/system", systemRoutes);
            httpServer.use("/api/global-shortcuts", globalShortcutRoutes);
            httpServer.use("/api/notification", notificationRoutes);
            httpServer.use("/api/dock", dockRoutes);
            httpServer.use("/api/menu", menuRoutes);
            httpServer.use("/api/window", windowRoutes);
            httpServer.use("/api/process", processRoutes);
            httpServer.use("/api/settings", settingsRoutes);
            httpServer.use("/api/shell", shellRoutes);
            httpServer.use("/api/context", contextMenuRoutes);
            httpServer.use("/api/menu-bar", menuBarRoutes);
            httpServer.use("/api/progress-bar", progressBarRoutes);
            httpServer.use("/api/power-monitor", powerMonitorRoutes);
            httpServer.use("/api/child-process", childProcessRoutes);
            httpServer.use("/api/broadcast", broadcastingRoutes);
            if (process.env.NODE_ENV === "development") {
                httpServer.use("/api/debug", debugRoutes);
            }
            const server = httpServer.listen(port, () => {
                resolve({
                    server,
                    port,
                });
            });
        });
    });
}
export default startAPIServer;
