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
import getPort from "get-port";
import middleware from "./api/middleware";
import clipboardRoutes from "./api/clipboard";
import appRoutes from "./api/app";
import screenRoutes from "./api/screen";
import dialogRoutes from "./api/dialog";
import debugRoutes from "./api/debug";
import broadcastingRoutes from "./api/broadcasting";
import systemRoutes from "./api/system";
import globalShortcutRoutes from "./api/globalShortcut";
import notificationRoutes from "./api/notification";
import dockRoutes from "./api/dock";
import menuRoutes from "./api/menu";
import menuBarRoutes from "./api/menuBar";
import windowRoutes from "./api/window";
import processRoutes from "./api/process";
import contextMenuRoutes from "./api/contextMenu";
import settingsRoutes from "./api/settings";
import shellRoutes from "./api/shell";
import progressBarRoutes from "./api/progressBar";
import powerMonitorRoutes from "./api/powerMonitor";
import childProcessRoutes from "./api/childProcess";
function startAPIServer(randomSecret) {
    return __awaiter(this, void 0, void 0, function* () {
        const port = yield getPort({
            port: getPort.makeRange(4000, 5000),
        });
        return new Promise((resolve, reject) => {
            const httpServer = express();
            httpServer.use(middleware(randomSecret));
            httpServer.use(bodyParser.json());
            httpServer.use("/api/clipboard", clipboardRoutes);
            httpServer.use("/api/app", appRoutes);
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
