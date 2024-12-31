import express from 'express'
import { broadcastToWindows } from '../utils.js';
const router = express.Router();

router.post('/', (req, res) => {
    const {event, payload} = req.body;

    broadcastToWindows("native-event", { event, payload });

    res.sendStatus(200)
})

export default router;
