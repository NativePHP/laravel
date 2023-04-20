import {join} from 'path'
import {nativeImage} from 'electron'
import express from 'express'
import bodyParser from 'body-parser'
import {notifyLaravel} from '.'
import getPort from 'get-port';
import {menubar} from 'menubar';

import {
    app,
    shell,
    BrowserWindow,
    Menu,
    dialog,
    globalShortcut,
    Notification
} from 'electron'
import icon from '../../../resources/icon.png?asset'
import contextMenu from 'electron-context-menu'

let windows = {}
let activeMenuBar = null;

const mapMenu = (menu) => {
    if (menu.submenu) {
        menu.submenu = menu.submenu.map(mapMenu)
    }

    if (menu.type === 'link') {
        return {
            label: menu.label,
            accelerator: menu.accelerator,
            click() {
                shell.openExternal(menu.url)
            }
        }
    }

    if (menu.type === 'event') {
        return {
            label: menu.label,
            click() {
                notifyLaravel('events', {
                    event: menu.event
                })
            }
        }
    }

    if (menu.type === 'role') {
        return {
            role: menu.role
        }
    }

    return menu
}

function startAPIServer() {
    return new Promise(async (resolve, reject) => {
        const httpServer = express()

        httpServer.use(bodyParser.json())

        httpServer.post('/api/notification', (req, res) => {
            const {title, body} = req.body

            new Notification({title, body}).show()

            res.sendStatus(200)
        })

        httpServer.post('/api/dialog', (req, res) => {
            const result = dialog.showOpenDialogSync({
                title: req.body.title,
                buttonLabel: req.body.buttonLabel,
                filters: req.body.filters,
                properties: req.body.properties
            })

            res.json({
                result
            })
        })

        httpServer.get('/api/process', (req, res) => {
            res.json({
                pid: process.pid,
                platform: process.platform,
                arch: process.arch,
                uptime: process.uptime()
            })
        })

        httpServer.post('/api/menubar/label', (req, res) => {
            res.sendStatus(200)

            const {label} = req.body

            activeMenuBar.tray.setTitle(label)
        })

        httpServer.post('/api/menubar', (req, res) => {
            res.sendStatus(200)

            const {id, width, height, url, alwaysOnTop, vibrancy, backgroundColor, transparency, icon, showDockIcon} = req.body

            if (! showDockIcon) {
                app.dock.hide();
            } else {
                app.dock.show();
            }

            activeMenuBar = menubar({
                icon,
                index: url,
                showDockIcon,
                browserWindow: {
                    width,
                    height,
                    alwaysOnTop,
                    vibrancy,
                    backgroundColor,
                    transparency,
                    webPreferences: {
                        nodeIntegration: true,
                        sandbox: false,
                        contextIsolation: false
                    }
                }
            });
            activeMenuBar.on('after-create-window', () => {
                require("@electron/remote/main").enable(activeMenuBar.window.webContents)
            });
            activeMenuBar.on('ready', () => {
                activeMenuBar.tray.setImage(nativeImage.createEmpty());
                activeMenuBar.on('show', () => {
                    notifyLaravel('events', {
                        event: '\\Native\\Laravel\\Events\\MenuBar\\MenuBarClicked',
                    })
                });
                activeMenuBar.tray.on('right-click', () => {
                    activeMenuBar.tray.popUpContextMenu(Menu.buildFromTemplate([
                        { role: 'quit' }
                    ]))
                });
                console.log("menubar ready")
            });
        });

        httpServer.post('/api/global-shortcut', (req, res) => {
            const {key, event} = req.body

            globalShortcut.register(key, () => {
                notifyLaravel('events', {
                    event,
                    payload: []
                })
            })

            res.sendStatus(200)
        })

        httpServer.post('/api/window/resize', (req, res) => {
            const {id, width, height} = req.body

            windows[id]?.setSize(parseInt(width), parseInt(height))

            res.sendStatus(200)
        })

        httpServer.post('/api/window/close', (req, res) => {
            const {id} = req.body

            if (windows[id]) {
                windows[id].close()
                delete windows[id]
            }
            return res.sendStatus(200)
        })

        httpServer.post('/api/context', (req, res) => {
            res.sendStatus(200)

            contextMenu({
                showLookUpSelection: false,
                showSearchWithGoogle: false,
                showInspectElement: false,
                prepend: (defaultActions, parameters, browserWindow) => {
                    return req.body.items.map(mapMenu)
                }
            })
        })

        httpServer.post('/api/window/open', (req, res) => {
            let {
                id,
                width,
                height,
                url,
                resizable,
                title,
                alwaysOnTop,
                titleBarStyle,
                vibrancy,
                backgroundColor,
                transparency
            } = req.body

            if (windows[id]) {
                windows[id].show()
                windows[id].focus()
                return res.sendStatus(200)
            }

            const window = new BrowserWindow({
                width: parseInt(width),
                height: parseInt(height),
                show: false,
                title,
                transparency,
                backgroundColor,
                alwaysOnTop,
                resizable,
                titleBarStyle,
                vibrancy,
                autoHideMenuBar: true,
                ...(process.platform === 'linux' ? {icon} : {}),
                webPreferences: {
                    backgroundThrottling: false,
                    spellcheck: false,
                    preload: join(__dirname, '../../../preload/index.js'),
                    sandbox: false
                }
            })

            window.on('focus', () => {
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
                if (windows[id]) {
                    delete windows[id]
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
            windows[id] = window

            res.sendStatus(200)
        })

        httpServer.post('/api/window/always-on-top', (req, res) => {
            const {id, alwaysOnTop} = req.body
            windows[id]?.setAlwaysOnTop(alwaysOnTop)

            res.sendStatus(200)
        })

        httpServer.post('/api/menu', (req, res) => {
            const menuEntries = req.body.items.map(mapMenu)

            const menu = Menu.buildFromTemplate(menuEntries)
            Menu.setApplicationMenu(menu)
            res.sendStatus(200)
        })

        httpServer.post('/api/dock', (req, res) => {
            const menuEntries = req.body.items.map(mapMenu)

            const menu = Menu.buildFromTemplate(menuEntries)
            app.dock.setMenu(menu)
            res.sendStatus(200)
        })

        const port = await getPort({
            port: getPort.makeRange(4000, 5000)
        });

        httpServer.listen(port, () => {
            resolve(port)
        })
    })
}


export default startAPIServer
