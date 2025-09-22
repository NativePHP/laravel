import express from 'express';
import { dialog } from 'electron';
const router = express.Router();
router.post('/message', (req, res) => {
    const { message, type, title, detail, buttons, defaultId, cancelId } = req.body;
    const result = dialog.showMessageBoxSync({
        message,
        type: type !== null && type !== void 0 ? type : undefined,
        title: title !== null && title !== void 0 ? title : undefined,
        detail: detail !== null && detail !== void 0 ? detail : undefined,
        buttons: buttons !== null && buttons !== void 0 ? buttons : undefined,
        defaultId: defaultId !== null && defaultId !== void 0 ? defaultId : undefined,
        cancelId: cancelId !== null && cancelId !== void 0 ? cancelId : undefined
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
