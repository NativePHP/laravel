import express from 'express';
import { app, Menu } from 'electron';
import { compileMenu } from './helper/index.js';
import state from '../state.js';

const router = express.Router();

router.post('/', (req, res) => {
    const menuEntries = req.body.items.map(compileMenu);

    const menu = Menu.buildFromTemplate(menuEntries);
    app.dock.setMenu(menu);

    res.sendStatus(200);
});

router.post('/show', (req, res) => {
    app.dock.show();

    res.sendStatus(200);
});

router.post('/hide', (req, res) => {
    app.dock.hide();

    res.sendStatus(200);
});

router.post('/icon', (req, res) => {
    app.dock.setIcon(req.body.path);

    res.sendStatus(200);
});

router.post('/bounce', (req, res) => {
    const { type } = req.body;

    state.dockBounce = app.dock.bounce(type);

    res.sendStatus(200);
});

router.post('/cancel-bounce', (req, res) => {
    app.dock.cancelBounce(state.dockBounce);

    res.sendStatus(200);
});

router.get('/badge', (req, res) => {
    res.json({
        label: app.dock.getBadge(),
    });
});

router.post('/badge', (req, res) => {
    app.dock.setBadge(req.body.label);

    res.sendStatus(200);
});

export default router;
