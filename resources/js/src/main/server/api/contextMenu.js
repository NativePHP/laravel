import express from 'express'
import {app, Menu} from 'electron'
import {mapMenu} from "./helper";
import contextMenu from "electron-context-menu";
const router = express.Router();

router.post('/', (req, res) => {
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

export default router;
