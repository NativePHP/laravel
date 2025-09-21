import express from 'express'
import { dialog } from 'electron'
const router = express.Router();

router.post('/message', (req, res) => {
    const { message, type, title, detail, buttons, defaultId, cancelId } = req.body;
    const result = dialog.showMessageBoxSync({
        message,
        type: type ?? undefined,
        title: title ?? undefined,
        detail: detail ?? undefined,
        buttons: buttons ?? undefined,
        defaultId: defaultId ?? undefined,
        cancelId: cancelId ?? undefined
    });
    res.json({
        result
    });
});

router.post('/error', (req, res) => {
    const { title, message } = req.body;

    dialog.showErrorBox(title, message);

    res.json({
        result: true
    });
});

export default router;
