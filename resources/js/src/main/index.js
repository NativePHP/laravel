import {app, BrowserWindow, Notification} from 'electron'
import { autoUpdater } from "electron-updater"
import {electronApp, optimizer, is} from '@electron-toolkit/utils'
import {notifyLaravel, startAPI, runScheduler, servePhpApp, serveWebsockets, retrieveNativePHPConfig} from './server'
import ps from 'ps-node'
import {resolve} from 'path'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'

let phpProcesses = [];
let websocketProcess;
let schedulerInterval;

require('@electron/remote/main').initialize()

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.whenReady().then(async () => {

    if (process.env.NODE_ENV === 'development') {
        app.dock.setIcon(defaultIcon)
    }

    // Default open or close DevTools by F12 in development
    // and ignore CommandOrControl + R in production.
    // see https://github.com/alex8088/electron-toolkit/tree/master/packages/utils
    app.on('browser-window-created', (_, window) => {
        optimizer.watchWindowShortcuts(window)
    })

    let nativePHPConfig = {};
    try {
        let {stdout} = await retrieveNativePHPConfig()
        nativePHPConfig = JSON.parse(stdout);
    } catch (e) {
        console.error(e);
    }

    // Set app user model id for windows
    electronApp.setAppUserModelId(nativePHPConfig?.app_id)

    const deepLinkProtocol = nativePHPConfig?.deeplink_scheme;
    if (deepLinkProtocol) {
        if (process.defaultApp) {
            if (process.argv.length >= 2) {
                app.setAsDefaultProtocolClient(deepLinkProtocol, process.execPath, [resolve(process.argv[1])])
            }
        } else {
            app.setAsDefaultProtocolClient(deepLinkProtocol)
        }
    }

    // Start PHP server and websockets
    const apiPort = await startAPI()
    console.log('API server started on port', apiPort);

    phpProcesses = await servePhpApp(apiPort)

    websocketProcess = serveWebsockets()

    await notifyLaravel('booted')

    if (nativePHPConfig?.updater?.enabled === true) {
        autoUpdater.checkForUpdatesAndNotify()
    }

    schedulerInterval = setInterval(() => {
        console.log("Running scheduler...")
        runScheduler(apiPort);
    }, 60 * 1000);

    app.on('activate', function () {
        // On macOS it's common to re-create a window in the app when the
        // dock icon is clicked and there are no other windows open.
        if (BrowserWindow.getAllWindows().length === 0) notifyLaravel('booted')
    })
})

app.on('open-url', (event, url) => {
    notifyLaravel('events', {
        event: '\\Native\\Laravel\\Events\\App\\OpenedFromURL',
        payload: [url]
    })
})

app.on('open-file', (event, path) => {
    notifyLaravel('events', {
        event: '\\Native\\Laravel\\Events\\App\\OpenFile',
        payload: [path]
    })
});

// Quit when all windows are closed, except on macOS. There, it's common
// for applications and their menu bar to stay active until the user quits
// explicitly with Cmd + Q.
app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        app.quit()
    }
})


function killChildProcesses() {
    let processes = [
        ...phpProcesses,
        websocketProcess,
    ];

    processes.forEach((process) => {
        try {
            console.log(`Killing process ${process.pid}`)
            ps.kill(process.pid, function( err ) {
                if (err) {
                    console.error( 'Error occurred while killing process %s', pid );
                }
                else {
                    console.log( 'Process %s has been killed!', pid );
                }
            });
        } catch (err) {
            console.error(err);
        }
    });
}

let processKilled = false;
app.on('before-quit', (e) => {
    if (schedulerInterval) {
        clearInterval(schedulerInterval);
    }

    killChildProcesses();
});
