import express from 'express';
import { Menu } from 'electron';
import { compileMenu } from './helper';

const router = express.Router();

router.post('/', (req, res) => {
    Menu.setApplicationMenu(null);

    const menuEntries = req.body.items.map(compileMenu);

    const menu = Menu.buildFromTemplate(menuEntries);

    Menu.setApplicationMenu(menu);

    res.sendStatus(200);
});

export default router;
