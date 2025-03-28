import express from 'express';
import { app } from 'electron';
const router = express.Router();
router.post('/quit', (req, res) => {
    app.quit();
    res.sendStatus(200);
});
router.post('/show', (req, res) => {
    app.show();
    res.sendStatus(200);
});
router.post('/hide', (req, res) => {
    app.hide();
    res.sendStatus(200);
});
router.get('/is-hidden', (req, res) => {
    res.json({
        is_hidden: app.isHidden(),
    });
});
router.get('/app-path', (req, res) => {
    res.json({
        path: app.getAppPath(),
    });
});
router.get('/path/:name', (req, res) => {
    res.json({
        path: app.getPath(req.params.name),
    });
});
router.get('/version', (req, res) => {
    res.json({
        version: app.getVersion(),
    });
});
router.post('/badge-count', (req, res) => {
    app.setBadgeCount(req.body.count);
    res.sendStatus(200);
});
router.get('/badge-count', (req, res) => {
    res.json({
        count: app.getBadgeCount(),
    });
});
router.post('/recent-documents', (req, res) => {
    app.addRecentDocument(req.body.path);
    res.sendStatus(200);
});
router.delete('/recent-documents', (req, res) => {
    app.clearRecentDocuments();
    res.sendStatus(200);
});
router.post('/open-at-login', (req, res) => {
    app.setLoginItemSettings({
        openAtLogin: req.body.open,
    });
    res.sendStatus(200);
});
router.get('/open-at-login', (req, res) => {
    res.json({
        open: app.getLoginItemSettings().openAtLogin,
    });
});
export default router;
