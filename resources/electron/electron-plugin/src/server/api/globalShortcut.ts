import express from 'express'
import {globalShortcut} from 'electron'
import {notifyLaravel} from "../utils.js";
const router = express.Router();

router.post('/', (req, res) => {
    const {key, event} = req.body

    globalShortcut.register(key, () => {
        notifyLaravel('events', {
            event,
            payload: [key]
        })
    })

    res.sendStatus(200)
})

router.delete('/', (req, res) => {
    const {key} = req.body

    globalShortcut.unregister(key)

    res.sendStatus(200)
});

router.get('/:key', (req, res) => {
    const {key} = req.params

    res.json({
        isRegistered: globalShortcut.isRegistered(key)
    });
});

export default router;
