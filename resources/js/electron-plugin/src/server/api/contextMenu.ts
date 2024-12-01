import express from 'express';
import { app, Menu } from 'electron';
import { compileMenu } from "./helper";
import contextMenu from "electron-context-menu";

const router = express.Router();

let contextMenuDisposable = null

router.delete('/', (req, res) => {
    res.sendStatus(200);

    if (contextMenuDisposable) {
        contextMenuDisposable();
        contextMenuDisposable = null;
    }
});

router.post('/', (req, res) => {
    res.sendStatus(200);

    if (contextMenuDisposable) {
        contextMenuDisposable();
        contextMenuDisposable = null;
    }

    contextMenuDisposable = contextMenu({
        showLookUpSelection: false,
        showSearchWithGoogle: false,
        showInspectElement: false,
        prepend: (defaultActions, parameters, browserWindow) => {
            return req.body.entries.map(compileMenu);
        },
    });
});

export default router;
