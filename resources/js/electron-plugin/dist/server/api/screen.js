import express from 'express';
import { screen } from 'electron';
const router = express.Router();
router.get('/displays', (req, res) => {
    res.json({
        displays: screen.getAllDisplays()
    });
});
router.get('/primary-display', (req, res) => {
    res.json({
        primaryDisplay: screen.getPrimaryDisplay()
    });
});
router.get('/cursor-position', (req, res) => {
    res.json(screen.getCursorScreenPoint());
});
router.get('/active', (req, res) => {
    const cursor = screen.getCursorScreenPoint();
    res.json(screen.getDisplayNearestPoint(cursor));
});
export default router;
