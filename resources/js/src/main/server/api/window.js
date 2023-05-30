import express from 'express'
import {BrowserWindow, clipboard, NativeImage} from 'electron'
import state from '../state'
import icon from "../../../../resources/icon.png";
import {join} from "path";
import {notifyLaravel} from "../index";
const router = express.Router();

router.post('/resize', (req, res) => {
    const {id, width, height} = req.body

    state.windows[id]?.setSize(parseInt(width), parseInt(height))

    res.sendStatus(200)
})

router.post('/close', (req, res) => {
    const {id} = req.body

    if (state.windows[id]) {
        state.windows[id].close()
        delete state.windows[id]
    }
    return res.sendStatus(200)
})

router.get('/current', (req, res) => {
    const currentWindow = Object.values(state.windows).find(window => window.id === BrowserWindow.getFocusedWindow().id)
    // Find object key with matching value
    const id = Object.keys(state.windows).find(key => state.windows[key] === currentWindow)

    res.json({
        id: id,
        x: currentWindow.getPosition()[0],
        y: currentWindow.getPosition()[1],
        width: currentWindow.getSize()[0],
        height: currentWindow.getSize()[1],
        title: currentWindow.getTitle(),
        alwaysOnTop: currentWindow.isAlwaysOnTop(),
    })
});

router.post('/always-on-top', (req, res) => {
    const {id, alwaysOnTop} = req.body
    state.windows[id]?.setAlwaysOnTop(alwaysOnTop)

    res.sendStatus(200)
});

router.post('/open', (req, res) => {
    let {
        id,
        x,
        y,
        frame,
        width,
        height,
        focusable,
        hasShadow,
        url,
        resizable,
        title,
        alwaysOnTop,
        titleBarStyle,
        vibrancy,
        backgroundColor,
        transparency
    } = req.body

    if (state.windows[id]) {
        state.windows[id].show()
        state.windows[id].focus()
        return res.sendStatus(200)
    }

    let preloadPath = join(__dirname, '../../../preload/index.js')
    if (process.env.NODE_ENV === 'development' || argumentEnv.TESTING == 1) {
        preloadPath = join(__dirname, '../preload/index.js')
    }

    const window = new BrowserWindow({
        width: parseInt(width),
        height: parseInt(height),
        frame: frame !== undefined ? frame : true,
        x,
        y,
        show: false,
        title,
        backgroundColor,
        transparent: transparency,
        alwaysOnTop,
        resizable,
        hasShadow,
        titleBarStyle,
        vibrancy,
        focusable,
        autoHideMenuBar: true,
        ...(process.platform === 'linux' ? {icon} : {}),
        webPreferences: {
            backgroundThrottling: false,
            spellcheck: false,
            preload: preloadPath,
            sandbox: false,
            contextIsolation: false,
            nodeIntegration: true,
        }
    })

    require("@electron/remote/main").enable(window.webContents)

    window.on('blur', () => {
        window.webContents.send('window:blur')
    });

    window.on('focus', () => {
        window.webContents.send('window:focus')
        notifyLaravel('events', {
            event: 'App\\Events\\WindowFocused',
            payload: [window.webContents.getURL()]
        })
    })

    window.on('minimize', () => {
        notifyLaravel('events', {
            event: 'App\\Events\\WindowMinimized',
            payload: [window.webContents.getURL()]
        })
    })

    window.on('page-title-updated', (evt) => {
        evt.preventDefault()
    })

    window.on('close', (evt) => {
        if (state.windows[id]) {
            delete state.windows[id]
        }
        notifyLaravel('events', {
            event: 'App\\Events\\WindowClosed',
            payload: [window.webContents.getURL()]
        })
    })

    // Append the window id to the url
    url += (url.indexOf('?') === -1 ? '?' : '&') + '_windowId=' + id

    window.loadURL(url)

    window.webContents.on('did-finish-load', () => {
        window.show()
    })
    state.windows[id] = window

    res.sendStatus(200)
})

export default router;
