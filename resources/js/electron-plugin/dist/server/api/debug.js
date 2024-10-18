import express from 'express';
import { broadcastToWindows } from '../utils';
const router = express.Router();
router.post('/log', (req, res) => {
    const { level, message, context } = req.body;
    broadcastToWindows('log', { level, message, context });
    res.sendStatus(200);
});
export default router;
