import express from 'express'
import {systemPreferences} from 'electron'
import {notifyLaravel} from "../index";
const router = express.Router();

router.get('/can-prompt-touch-id', (req, res) => {
    res.json({
        result: systemPreferences.canPromptTouchID(),
    })
});

router.post('/prompt-touch-id', async (req, res) => {
    try {
        await systemPreferences.promptTouchID(req.body.reason)

        res.sendStatus(200);
    } catch (e) {
        res.status(400).json({
            error: e.message,
        })
    }
});

export default router;
