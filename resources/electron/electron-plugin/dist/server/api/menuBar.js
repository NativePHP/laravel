import express from "express";
import { app, Menu, Tray } from "electron";
import { compileMenu } from "./helper/index.js";
import state from "../state.js";
import { menubar } from "../../libs/menubar/index.js";
import { notifyLaravel } from "../utils.js";
import { enable } from "@electron/remote/main/index.js";
import mergePreferences from "../webPreferences.js";
const router = express.Router();
router.post("/label", (req, res) => {
    var _a;
    res.sendStatus(200);
    const { label } = req.body;
    (_a = state.tray) === null || _a === void 0 ? void 0 : _a.setTitle(label);
});
router.post("/tooltip", (req, res) => {
    var _a;
    res.sendStatus(200);
    const { tooltip } = req.body;
    (_a = state.tray) === null || _a === void 0 ? void 0 : _a.setToolTip(tooltip);
});
router.post("/icon", (req, res) => {
    var _a;
    res.sendStatus(200);
    const { icon } = req.body;
    (_a = state.tray) === null || _a === void 0 ? void 0 : _a.setImage(icon);
});
router.post("/context-menu", (req, res) => {
    var _a;
    res.sendStatus(200);
    const { contextMenu } = req.body;
    (_a = state.tray) === null || _a === void 0 ? void 0 : _a.setContextMenu(buildMenu(contextMenu));
});
router.post("/show", (req, res) => {
    res.sendStatus(200);
    state.activeMenuBar.showWindow();
});
router.post("/hide", (req, res) => {
    res.sendStatus(200);
    state.activeMenuBar.hideWindow();
});
router.post("/resize", (req, res) => {
    res.sendStatus(200);
    const { width, height } = req.body;
    state.activeMenuBar.window.setSize(width, height);
});
router.post("/create", (req, res) => {
    res.sendStatus(200);
    let shouldSendCreatedEvent = true;
    if (state.activeMenuBar) {
        state.activeMenuBar.tray.destroy();
        shouldSendCreatedEvent = false;
    }
    const { width, height, url, label, alwaysOnTop, vibrancy, backgroundColor, transparency, icon, showDockIcon, onlyShowContextMenu, windowPosition, showOnAllWorkspaces, contextMenu, tooltip, resizable, webPreferences, event, } = req.body;
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
            showOnAllWorkspaces: showOnAllWorkspaces !== null && showOnAllWorkspaces !== void 0 ? showOnAllWorkspaces : false,
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
                webPreferences: mergePreferences(webPreferences)
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
                    event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarHidden"
                });
            });
            state.activeMenuBar.on("show", () => {
                notifyLaravel("events", {
                    event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarShown"
                });
            });
        });
    }
});
function eventsForTray(tray, onlyShowContextMenu, contextMenu, shouldSendCreatedEvent) {
    if (shouldSendCreatedEvent) {
        notifyLaravel("events", {
            event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarCreated"
        });
    }
    tray.on("drop-files", (event, files) => {
        notifyLaravel("events", {
            event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarDroppedFiles",
            payload: [
                files
            ]
        });
    });
    tray.on('click', (combo, bounds, position) => {
        notifyLaravel('events', {
            event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarClicked",
            payload: {
                combo,
                bounds,
                position,
            },
        });
    });
    tray.on("right-click", (combo, bounds) => {
        notifyLaravel("events", {
            event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarRightClicked",
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
            event: "\\Native\\Desktop\\Events\\MenuBar\\MenuBarDoubleClicked",
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
