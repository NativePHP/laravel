import express from "express";
import { app, Menu, Tray } from "electron";
import { compileMenu } from "./helper/index.js";
import state from "../state.js";
import { menubar } from "menubar";
import { notifyLaravel } from "../utils.js";
import { fileURLToPath } from 'url';
import { enable } from "@electron/remote/main/index.js";
const router = express.Router();
router.post("/label", (req, res) => {
    res.sendStatus(200);
    const { label } = req.body;
    state.tray.setTitle(label);
});
router.post("/tooltip", (req, res) => {
    res.sendStatus(200);
    const { tooltip } = req.body;
    state.tray.setToolTip(tooltip);
});
router.post("/icon", (req, res) => {
    res.sendStatus(200);
    const { icon } = req.body;
    state.tray.setImage(icon);
});
router.post("/context-menu", (req, res) => {
    res.sendStatus(200);
    const { contextMenu } = req.body;
    state.tray.setContextMenu(buildMenu(contextMenu));
});
router.post("/show", (req, res) => {
    res.sendStatus(200);
    state.activeMenuBar.showWindow();
});
router.post("/hide", (req, res) => {
    res.sendStatus(200);
    state.activeMenuBar.hideWindow();
});
router.post("/create", (req, res) => {
    res.sendStatus(200);
    let shouldSendCreatedEvent = true;
    if (state.activeMenuBar) {
        state.activeMenuBar.tray.destroy();
        shouldSendCreatedEvent = false;
    }
    const { width, height, url, label, alwaysOnTop, vibrancy, backgroundColor, transparency, icon, showDockIcon, onlyShowContextMenu, windowPosition, contextMenu, tooltip, resizable, event, } = req.body;
    if (onlyShowContextMenu) {
        const tray = new Tray(icon || state.icon.replace("icon.png", "IconTemplate.png"));
        tray.setContextMenu(buildMenu(contextMenu));
        tray.setToolTip(tooltip);
        tray.setTitle(label);
        eventsForTray(tray, onlyShowContextMenu, contextMenu, shouldSendCreatedEvent);
        state.tray = tray;
        if (!showDockIcon) {
            app.dock.hide();
        }
    }
    else {
        state.activeMenuBar = menubar({
            icon: icon || state.icon.replace("icon.png", "IconTemplate.png"),
            preloadWindow: true,
            tooltip,
            index: url,
            showDockIcon,
            showOnAllWorkspaces: false,
            windowPosition: windowPosition !== null && windowPosition !== void 0 ? windowPosition : "trayCenter",
            activateWithApp: false,
            browserWindow: {
                width,
                height,
                resizable,
                alwaysOnTop,
                vibrancy,
                backgroundColor,
                transparent: transparency,
                webPreferences: {
                    preload: fileURLToPath(new URL('../../electron-plugin/dist/preload/index.mjs', import.meta.url)),
                    nodeIntegration: true,
                    sandbox: false,
                    contextIsolation: false,
                }
            }
        });
        state.activeMenuBar.on("after-create-window", () => {
            enable(state.activeMenuBar.window.webContents);
        });
        state.activeMenuBar.on("ready", () => {
            eventsForTray(state.activeMenuBar.tray, onlyShowContextMenu, contextMenu, shouldSendCreatedEvent);
            state.tray = state.activeMenuBar.tray;
            state.tray.setTitle(label);
            state.activeMenuBar.on("hide", () => {
                notifyLaravel("events", {
                    event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarHidden"
                });
            });
            state.activeMenuBar.on("show", () => {
                notifyLaravel("events", {
                    event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarShown"
                });
            });
        });
    }
});
function eventsForTray(tray, onlyShowContextMenu, contextMenu, shouldSendCreatedEvent) {
    if (shouldSendCreatedEvent) {
        notifyLaravel("events", {
            event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarCreated"
        });
    }
    tray.on("drop-files", (event, files) => {
        notifyLaravel("events", {
            event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarDroppedFiles",
            payload: [
                files
            ]
        });
    });
    tray.on('click', (combo, bounds, position) => {
        notifyLaravel('events', {
            event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarClicked",
            payload: {
                combo,
                bounds,
                position,
            },
        });
    });
    tray.on("right-click", (combo, bounds) => {
        notifyLaravel("events", {
            event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarRightClicked",
            payload: {
                combo,
                bounds,
            }
        });
        if (!onlyShowContextMenu) {
            state.activeMenuBar.hideWindow();
            tray.popUpContextMenu(buildMenu(contextMenu));
        }
    });
    tray.on('double-click', (combo, bounds) => {
        notifyLaravel('events', {
            event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarDoubleClicked",
            payload: {
                combo,
                bounds,
            },
        });
    });
}
function buildMenu(contextMenu) {
    let menu = Menu.buildFromTemplate([{ role: "quit" }]);
    if (contextMenu) {
        const menuEntries = contextMenu.map(compileMenu);
        menu = Menu.buildFromTemplate(menuEntries);
    }
    return menu;
}
export default router;
