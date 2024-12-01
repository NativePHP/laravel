import express from 'express';
import { BrowserWindow } from 'electron';
import state from '../state';
import { join } from 'path';
import { notifyLaravel, goToUrl, appendWindowIdToUrl } from '../utils';
import windowStateKeeper from 'electron-window-state';
const router = express.Router();
router.post('/maximize', (req, res) => {
    var _a;
    const { id } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.maximize();
    res.sendStatus(200);
});
router.post('/minimize', (req, res) => {
    var _a;
    const { id } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.minimize();
    res.sendStatus(200);
});
router.post('/resize', (req, res) => {
    var _a;
    const { id, width, height } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.setSize(parseInt(width), parseInt(height));
    res.sendStatus(200);
});
router.post('/title', (req, res) => {
    var _a;
    const { id, title } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.setTitle(title);
    res.sendStatus(200);
});
router.post('/url', (req, res) => {
    const { id, url } = req.body;
    goToUrl(url, id);
    res.sendStatus(200);
});
router.post('/closable', (req, res) => {
    var _a;
    const { id, closable } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.setClosable(closable);
    res.sendStatus(200);
});
router.post('/show-dev-tools', (req, res) => {
    var _a;
    const { id } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.webContents.openDevTools();
    res.sendStatus(200);
});
router.post('/hide-dev-tools', (req, res) => {
    var _a;
    const { id } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.webContents.closeDevTools();
    res.sendStatus(200);
});
router.post('/position', (req, res) => {
    var _a;
    const { id, x, y, animate } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.setPosition(parseInt(x), parseInt(y), animate);
    res.sendStatus(200);
});
router.post('/reload', (req, res) => {
    var _a;
    const { id } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.reload();
    res.sendStatus(200);
});
router.post('/close', (req, res) => {
    const { id } = req.body;
    if (state.windows[id]) {
        state.windows[id].close();
        delete state.windows[id];
    }
    return res.sendStatus(200);
});
router.post('/hide', (req, res) => {
    const { id } = req.body;
    if (state.windows[id]) {
        state.windows[id].hide();
    }
    return res.sendStatus(200);
});
router.post('/always-on-top', (req, res) => {
    var _a;
    const { id, alwaysOnTop } = req.body;
    (_a = state.windows[id]) === null || _a === void 0 ? void 0 : _a.setAlwaysOnTop(alwaysOnTop);
    res.sendStatus(200);
});
router.get('/current', (req, res) => {
    const currentWindow = Object.values(state.windows).find(window => window.id === BrowserWindow.getFocusedWindow().id);
    const id = Object.keys(state.windows).find(key => state.windows[key] === currentWindow);
    res.json(getWindowData(id));
});
router.get('/all', (req, res) => {
    res.json(Object.keys(state.windows).map(id => getWindowData(id)));
});
router.get('/get/:id', (req, res) => {
    const { id } = req.params;
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
    };
}
router.post('/open', (req, res) => {
    let { id, x, y, frame, width, height, minWidth, minHeight, maxWidth, maxHeight, focusable, hasShadow, url, resizable, movable, minimizable, maximizable, closable, title, alwaysOnTop, titleBarStyle, trafficLightPosition, vibrancy, backgroundColor, transparency, showDevTools, fullscreen, fullscreenable, kiosk, autoHideMenuBar, webPreferences, } = req.body;
    if (state.windows[id]) {
        state.windows[id].show();
        state.windows[id].focus();
        return res.sendStatus(200);
    }
    let preloadPath = join(__dirname, '../../electron-plugin/dist/preload/index.js');
    const defaultWebPreferences = {
        backgroundThrottling: false,
        spellcheck: false,
        preload: preloadPath,
        sandbox: false,
        contextIsolation: false,
        nodeIntegration: true,
    };
    let windowState = undefined;
    if (req.body.rememberState === true) {
        windowState = windowStateKeeper({
            file: `window-state-${id}.json`,
            defaultHeight: parseInt(height),
            defaultWidth: parseInt(width),
        });
    }
    const window = new BrowserWindow(Object.assign(Object.assign({ width: (windowState === null || windowState === void 0 ? void 0 : windowState.width) || parseInt(width), height: (windowState === null || windowState === void 0 ? void 0 : windowState.height) || parseInt(height), frame: frame !== undefined ? frame : true, x: (windowState === null || windowState === void 0 ? void 0 : windowState.x) || x, y: (windowState === null || windowState === void 0 ? void 0 : windowState.y) || y, minWidth: minWidth, minHeight: minHeight, maxWidth: maxWidth, maxHeight: maxHeight, show: false, title,
        backgroundColor, transparent: transparency, alwaysOnTop,
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
        autoHideMenuBar }, (process.platform === 'linux' ? { icon: state.icon } : {})), { webPreferences: Object.assign(Object.assign({}, webPreferences), defaultWebPreferences), fullscreen,
        fullscreenable,
        kiosk }));
    if ((process.env.NODE_ENV === 'development' || showDevTools === true) && showDevTools !== false) {
        window.webContents.openDevTools();
    }
    require("@electron/remote/main").enable(window.webContents);
    if (req.body.rememberState === true) {
        windowState.manage(window);
    }
    window.on('blur', () => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowBlurred',
            payload: [id]
        });
    });
    window.on('focus', () => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowFocused',
            payload: [id]
        });
    });
    window.on('minimize', () => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowMinimized',
            payload: [id]
        });
    });
    window.on('maximize', () => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowMaximized',
            payload: [id]
        });
    });
    window.on('show', () => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowShown',
            payload: [id]
        });
    });
    window.on('resized', () => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowResized',
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
            event: 'Native\\Laravel\\Events\\Windows\\WindowClosed',
            payload: [id]
        });
    });
    window.on('hide', (evt) => {
        notifyLaravel('events', {
            event: 'Native\\Laravel\\Events\\Windows\\WindowHidden',
            payload: [id]
        });
    });
    url = appendWindowIdToUrl(url, id);
    window.loadURL(url);
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
