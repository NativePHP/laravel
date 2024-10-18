import express from 'express'
import { screen } from 'electron'
const router = express.Router();

router.get('/displays', (req, res) => {
    res.json({
        displays: screen.getAllDisplays()
    })
});

router.get('/primary-display', (req, res) => {
    res.json({
        primaryDisplay: screen.getPrimaryDisplay()
    })
});

router.get('/cursor-position', (req, res) => {
    res.json(screen.getCursorScreenPoint())
});

export default router;
