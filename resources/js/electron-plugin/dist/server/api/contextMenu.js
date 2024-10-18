import express from 'express';
import { mapMenu } from "./helper";
import contextMenu from "electron-context-menu";
const router = express.Router();
let contextMenuDisposable = null;
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
            return req.body.entries.map(mapMenu);
        }
    });
});
export default router;
