import express from 'express';
import { BrowserWindow } from 'electron';
import state from '../state.js';
import { fileURLToPath } from 'url'
import { notifyLaravel, goToUrl, appendWindowIdToUrl } from '../utils.js';
import windowStateKeeper from 'electron-window-state';
import mergePreferences from '../webPreferences.js'

import {enable} from "@electron/remote/main/index.js";

const router = express.Router();

router.post('/maximize', (req, res) => {
    const {id} = req.body;

    state.windows[id]?.maximize();

    res.sendStatus(200);
});

router.post('/minimize', (req, res) => {
    const {id} = req.body;

    state.windows[id]?.minimize();

    res.sendStatus(200);
});

router.post('/resize', (req, res) => {
    const {id, width, height} = req.body;

    state.windows[id]?.setSize(parseInt(width), parseInt(height));

    res.sendStatus(200);
});

router.post('/title', (req, res) => {
    const {id, title} = req.body;

    state.windows[id]?.setTitle(title);

    res.sendStatus(200);
});

router.post('/url', (req, res) => {
    const {id, url} = req.body;

    goToUrl(url, id);

    res.sendStatus(200);
});

router.post('/closable', (req, res) => {
    const {id, closable} = req.body;

    state.windows[id]?.setClosable(closable);

    res.sendStatus(200);
});

router.post('/show-dev-tools', (req, res) => {
    const {id} = req.body;

    state.windows[id]?.webContents.openDevTools();

    res.sendStatus(200);
});

router.post('/hide-dev-tools', (req, res) => {
    const {id} = req.body;

    state.windows[id]?.webContents.closeDevTools();

    res.sendStatus(200);
});

router.post('/set-zoom-factor', (req, res) => {
    const {id, zoomFactor} = req.body;

    state.windows[id]?.webContents.setZoomFactor(parseFloat(zoomFactor));

    res.sendStatus(200);
});

router.post('/position', (req, res) => {
    const {id, x, y, animate} = req.body;

    state.windows[id]?.setPosition(parseInt(x), parseInt(y), animate);

    res.sendStatus(200);
});

router.post('/reload', (req, res) => {
    const {id} = req.body;

    state.windows[id]?.reload();

    res.sendStatus(200);
});

router.post('/close', (req, res) => {
    const {id} = req.body;

    if (state.windows[id]) {
        state.windows[id].close();
        delete state.windows[id];
    }

    res.sendStatus(200);
});

router.post('/hide', (req, res) => {
    const {id} = req.body;

    if (state.windows[id]) {
        state.windows[id].hide();
    }

    res.sendStatus(200);
});

router.post('/show', (req, res) => {
    const { id } = req.body;

    if (state.windows[id]) {
        state.windows[id].show();
    }

    res.sendStatus(200);
});

router.post('/always-on-top', (req, res) => {
    const {id, alwaysOnTop} = req.body;

    state.windows[id]?.setAlwaysOnTop(alwaysOnTop);

    res.sendStatus(200);
});

router.get('/current', (req, res) => {
    // Find the current window object
    const currentWindow = Object.values(state.windows).find(window => window.id === BrowserWindow.getFocusedWindow().id);

    // Get the developer-assigned id for that window
    const id = Object.keys(state.windows).find(key => state.windows[key] === currentWindow);

    res.json(getWindowData(id));
});

router.get('/all', (req, res) => {
    res.json(
        Object.keys(state.windows).map(id => getWindowData(id))
    );
});

router.get('/get/:id', (req, res) => {
    const {id} = req.params;

    if (state.windows[id] === undefined) {
        res.sendStatus(404);
        return;
    }

    res.json(getWindowData(id));
});

function getWindowData(id) {
    const currentWindow = state.windows[id];

    if (state.windows[id] === undefined) {
        throw `Window [${id}] not found`;
    }

    return {
        id: id,
        x: currentWindow.getPosition()[0],
        y: currentWindow.getPosition()[1],
        width: currentWindow.getSize()[0],
        height: currentWindow.getSize()[1],
        title: currentWindow.getTitle(),
        alwaysOnTop: currentWindow.isAlwaysOnTop(),
        url: currentWindow.webContents.getURL(),
        autoHideMenuBar: currentWindow.isMenuBarAutoHide(),
        fullscreen: currentWindow.isFullScreen(),
        fullscreenable: currentWindow.isFullScreenable(),
        kiosk: currentWindow.isKiosk(),
        devToolsOpen: currentWindow.webContents.isDevToolsOpened(),
        resizable: currentWindow.isResizable(),
        movable: currentWindow.isMovable(),
        minimizable: currentWindow.isMinimizable(),
        maximizable: currentWindow.isMaximizable(),
        closable: currentWindow.isClosable(),
        focusable: currentWindow.isFocusable(),
        focused: currentWindow.isFocused(),
        hasShadow: currentWindow.hasShadow(),
        // frame: currentWindow.frame(),
        // titleBarStyle: currentWindow.getTitleBarStyle(),
        // trafficLightPosition: currentWindow.getTrafficLightPosition(),
    };
}

router.post('/open', (req, res) => {
    let {
        id,
        x,
        y,
        frame,
        width,
        height,
        minWidth,
        minHeight,
        maxWidth,
        maxHeight,
        focusable,
        skipTaskbar,
        hiddenInMissionControl,
        hasShadow,
        url,
        resizable,
        movable,
        minimizable,
        maximizable,
        closable,
        title,
        alwaysOnTop,
        titleBarStyle,
        trafficLightPosition,
        vibrancy,
        backgroundColor,
        transparency,
        showDevTools,
        fullscreen,
        fullscreenable,
        kiosk,
        autoHideMenuBar,
        webPreferences,
        zoomFactor,
        preventLeaveDomain,
        preventLeavePage,
        suppressNewWindows,
    } = req.body;

    if (state.windows[id]) {
        state.windows[id].show();
        state.windows[id].focus();
        res.sendStatus(200);

        return;
    }

    let windowState: windowStateKeeper.State | undefined = undefined;

    if (req.body.rememberState === true) {
        windowState = windowStateKeeper({
            file: `window-state-${id}.json`,
            defaultHeight: parseInt(height),
            defaultWidth: parseInt(width),
        });
    }

    const window = new BrowserWindow({
        width: resizable
            ? windowState?.width || parseInt(width)
            : parseInt(width),
        height: resizable
            ? windowState?.height || parseInt(height)
            : parseInt(height),
        frame: frame !== undefined ? frame : true,
        x: windowState?.x || x,
        y: windowState?.y || y,
        minWidth: minWidth,
        minHeight: minHeight,
        maxWidth: maxWidth,
        maxHeight: maxHeight,
        show: false,
        title,
        backgroundColor,
        transparent: transparency,
        alwaysOnTop,
        resizable,
        movable,
        minimizable,
        maximizable,
        closable,
        hasShadow,
        titleBarStyle,
        trafficLightPosition,
        vibrancy,
        focusable,
        skipTaskbar,
        hiddenInMissionControl,
        autoHideMenuBar,
        ...(process.platform === 'linux' ? {icon: state.icon} : {}),
        webPreferences: mergePreferences(webPreferences),
        fullscreen,
        fullscreenable,
        kiosk,
    });

    if ((process.env.NODE_ENV === 'development' || showDevTools === true) && showDevTools !== false) {
        window.webContents.openDevTools();
    }

    enable(window.webContents);

    if (req.body.rememberState === true) {
        windowState?.manage(window);
    }

    if (suppressNewWindows) {
        window.webContents.setWindowOpenHandler(() => {
            return { action: "deny" };
        });
    }

    window.on('blur', () => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowBlurred',
            payload: [id]
        });
    });

    window.on('focus', () => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowFocused',
            payload: [id]
        });
    });

    window.on('minimize', () => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowMinimized',
            payload: [id]
        });
    });

    window.on('maximize', () => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowMaximized',
            payload: [id]
        });
    });

    window.on('show', () => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowShown',
            payload: [id]
        });
    });

    window.on('resized', () => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowResized',
            payload: [id, window.getSize()[0], window.getSize()[1]]
        });
    });

    window.on('page-title-updated', (evt) => {
        evt.preventDefault();
    });

    window.on('close', (evt) => {
        if (state.windows[id]) {
            delete state.windows[id];
        }

        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowClosed',
            payload: [id]
        });
    });

    // @ts-ignore
    window.on('hide', (evt) => {
        notifyLaravel('events', {
            event: 'Native\\Desktop\\Events\\Windows\\WindowHidden',
            payload: [id]
        });
    });

    url = appendWindowIdToUrl(url, id);

    window.loadURL(url);

    window.webContents.on('dom-ready', () => {
        window.webContents.setZoomFactor(parseFloat(zoomFactor));
    });

    if (preventLeaveDomain || preventLeavePage) {
        window.webContents.on('will-navigate', (event, target) => {
            const origUrl = new URL(url);
            const targetUrl = new URL(target);

            if (preventLeaveDomain && targetUrl.hostname !== origUrl.hostname) {
                event.preventDefault();
            }

            if (preventLeavePage && (targetUrl.origin !== origUrl.origin || targetUrl.pathname !== origUrl.pathname)) {
                event.preventDefault();
            }
        });
    }

    window.webContents.on('did-finish-load', () => {
        window.show();
    });

    window.webContents.on('did-fail-load', (event) => {
        console.error('failed to open window...', event);
    });

    state.windows[id] = window;

    res.sendStatus(200);
});

export default router;
