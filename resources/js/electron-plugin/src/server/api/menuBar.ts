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
    onlyShowContextWindow,
    windowPosition,
    contextMenu
  } = req.body;

  if (onlyShowContextWindow === true) {
    const tray = new Tray(icon || state.icon.replace("icon.png", "IconTemplate.png"));
    tray.setContextMenu(buildMenu(contextMenu));

    state.activeMenuBar = menubar({
      tray,
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
      index: url,
      showDockIcon,
      showOnAllWorkspaces: false,
      windowPosition: windowPosition ?? "trayCenter",
      browserWindow: {
        width,
        height,
        alwaysOnTop,
        vibrancy,
        backgroundColor,
        transparent: transparency,
        webPreferences: {
          nodeIntegration: true,
          sandbox: false,
          contextIsolation: false
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

    if (onlyShowContextWindow !== true) {
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
