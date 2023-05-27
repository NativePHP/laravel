import express from 'express'
import {Menu, nativeImage} from 'electron'
import {mapMenu} from "./helper";
import state from "../state"
import {menubar} from 'menubar';
import {notifyLaravel} from "../index";
const router = express.Router();

router.post('/api/menubar/label', (req, res) => {
    res.sendStatus(200)

    const {label} = req.body

    state.activeMenuBar.tray.setTitle(label)
})

router.post('/api/menubar', (req, res) => {
    res.sendStatus(200)

    const {id, width, height, url, alwaysOnTop, vibrancy, backgroundColor, transparency, icon, showDockIcon} = req.body

    if (! showDockIcon) {
        //app.dock.hide();
    } else {
        //app.dock.show();
    }

    state.activeMenuBar = menubar({
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

export default router;
