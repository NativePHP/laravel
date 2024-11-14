import express from "express";
import { Menu, Tray } from "electron";
import { mapMenu } from "./helper";
import state from "../state";
import { menubar } from "menubar";
import { notifyLaravel } from "../utils";
import { join } from "path";

const router = express.Router();

router.post("/label", (req, res) => {
    res.sendStatus(200);

    const { label } = req.body;

    state.activeMenuBar.tray.setTitle(label);
});

router.post("/tooltip", (req, res) => {
    res.sendStatus(200);

    const { tooltip } = req.body;

    state.activeMenuBar.tray.setToolTip(tooltip);
});

router.post("/icon", (req, res) => {
    res.sendStatus(200);

    const { icon } = req.body;

    state.activeMenuBar.tray.setImage(icon);
});

router.post("/context-menu", (req, res) => {
    res.sendStatus(200);

    const { contextMenu } = req.body;

    state.activeMenuBar.tray.setContextMenu(buildMenu(contextMenu));
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

    const {
        width,
        height,
        url,
        label,
        alwaysOnTop,
        vibrancy,
        backgroundColor,
        transparency,
        icon,
        showDockIcon,
        onlyShowContextMenu,
        windowPosition,
        contextMenu,
        tooltip,
        resizable,
        event,
    } = req.body;

    if (onlyShowContextMenu) {
        const tray = new Tray(icon || state.icon.replace("icon.png", "IconTemplate.png"));

        tray.setContextMenu(buildMenu(contextMenu));

        if (event) {
            tray.on('click', (e) => {
                notifyLaravel('events', {
                    event,
                    payload: e,
                });
            });
        }

        state.activeMenuBar = menubar({
            tray,
            tooltip,
            index: false,
            showDockIcon,
            showOnAllWorkspaces: false,
            browserWindow: {
                show: false,
                width: 0,
                height: 0,
            }
        });
    } else {
        state.activeMenuBar = menubar({
            icon: icon || state.icon.replace("icon.png", "IconTemplate.png"),
            preloadWindow: true,
            tooltip,
            index: url,
            showDockIcon,
            showOnAllWorkspaces: false,
            windowPosition: windowPosition ?? "trayCenter",
            browserWindow: {
                width,
                height,
                resizable,
                alwaysOnTop,
                vibrancy,
                backgroundColor,
                transparent: transparency,
                webPreferences: {
                    preload: join(__dirname, '../../electron-plugin/dist/preload/index.js'),
                    nodeIntegration: true,
                    sandbox: false,
                    contextIsolation: false,
                }
            }
        });

        state.activeMenuBar.on("after-create-window", () => {
            require("@electron/remote/main").enable(state.activeMenuBar.window.webContents);
        });
    }

    state.activeMenuBar.on("ready", () => {
        state.activeMenuBar.tray.setTitle(label);

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

        state.activeMenuBar.tray.on("drop-files", (event, files) => {
            notifyLaravel("events", {
                event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarDroppedFiles",
                payload: [
                    files
                ]
            });
        });

        if (! onlyShowContextMenu) {
            state.activeMenuBar.tray.on("right-click", () => {
                notifyLaravel("events", {
                    event: "\\Native\\Laravel\\Events\\MenuBar\\MenuBarContextMenuOpened"
                });

                state.activeMenuBar.tray.popUpContextMenu(buildMenu(contextMenu));
            });
        }
    });
});

function buildMenu(contextMenu) {
    let menu = Menu.buildFromTemplate([{ role: "quit" }]);

    if (contextMenu) {
        const menuEntries = contextMenu.map(mapMenu);
        menu = Menu.buildFromTemplate(menuEntries);
    }

    return menu;
}

export default router;
