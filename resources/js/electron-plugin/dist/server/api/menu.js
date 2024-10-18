import express from 'express';
import { Menu } from 'electron';
import { mapMenu } from "./helper";
const router = express.Router();
router.post('/', (req, res) => {
    const menuEntries = req.body.items.map(mapMenu);
    const menu = Menu.buildFromTemplate(menuEntries);
    Menu.setApplicationMenu(menu);
    res.sendStatus(200);
});
export default router;
