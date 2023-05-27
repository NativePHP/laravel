import {join} from 'path'
import {nativeImage} from 'electron'
import express from 'express'
import bodyParser from 'body-parser'
import {notifyLaravel} from '.'
import getPort from 'get-port';
import middleware from './api/middleware'

import clipboardRoutes from './api/clipboard'
import appRoutes from './api/app'
import screenRoutes from './api/screen'
import dialogRoutes from './api/dialog'
import systemRoutes from './api/system'
import globalShortcutRoutes from './api/globalShortcut'
import notificationRoutes from './api/notification'
import dockRoutes from './api/dock'
import menuRoutes from './api/menu'
import menubarRoutes from './api/menubar'
import windowRoutes from './api/window'
import processRoutes from './api/process'
import contextMenuRoutes from './api/contextMenu'
import progressBarRoutes from './api/progressBar'

import icon from '../../../resources/icon.png?asset'

function startAPIServer(randomSecret) {
    return new Promise(async (resolve, reject) => {
        const httpServer = express()
        //httpServer.use(middleware(randomSecret));
        httpServer.use(bodyParser.json())
        httpServer.use('/api/clipboard', clipboardRoutes);
        httpServer.use('/api/app', appRoutes);
        httpServer.use('/api/screen', screenRoutes);
        httpServer.use('/api/dialog', dialogRoutes);
        httpServer.use('/api/system', systemRoutes);
        httpServer.use('/api/global-shortcuts', globalShortcutRoutes);
        httpServer.use('/api/notification', notificationRoutes);
        httpServer.use('/api/dock', dockRoutes);
        httpServer.use('/api/menu', menuRoutes);
        httpServer.use('/api/window', windowRoutes);
        httpServer.use('/api/process', processRoutes);
        httpServer.use('/api/context', contextMenuRoutes);
        httpServer.use('/api/progress-bar', progressBarRoutes);

        const port = await getPort({
            port: getPort.makeRange(4000, 5000)
        });

        httpServer.listen(port, () => {
            resolve(port)
        })
    })
}


export default startAPIServer
