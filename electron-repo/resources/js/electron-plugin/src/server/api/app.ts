import express from 'express'
import { app } from 'electron'
const router = express.Router();

router.post('/quit', (req, res) => {
    app.quit()
    res.sendStatus(200);
});

router.post('/relaunch', (req, res) => {
    app.relaunch()
    app.quit()
});

router.post('/show', (req, res) => {
    app.show()
    res.sendStatus(200);
});

router.post('/hide', (req, res) => {
    app.hide()
    res.sendStatus(200);
});

router.get('/is-hidden', (req, res) => {
    res.json({
        is_hidden: app.isHidden(),
    })
});

router.get('/locale', (req, res) => {
    res.json({
        locale: app.getLocale(),
    })
});

router.get('/locale-country-code', (req, res) => {
    res.json({
        locale_country_code: app.getLocaleCountryCode(),
    })
});

router.get('/system-locale', (req, res) => {
    res.json({
        system_locale: app.getSystemLocale(),
    })
});

router.get('/app-path', (req, res) => {
    res.json({
        path: app.getAppPath(),
    })
});

router.get('/path/:name', (req, res) => {
    res.json({
        // @ts-ignore
        path: app.getPath(req.params.name),
    })
});

router.get('/version', (req, res) => {
    res.json({
        version: app.getVersion(),
    })
});

router.post('/badge-count', (req, res) => {
    app.setBadgeCount(req.body.count)
    res.sendStatus(200);
});

router.get('/badge-count', (req, res) => {
    res.json({
        count: app.getBadgeCount(),
    })
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

router.get('/is-emoji-panel-supported', (req, res) => {
    res.json({
        supported: app.isEmojiPanelSupported(),
    });
});

router.post('/show-emoji-panel', (req, res) => {
    app.showEmojiPanel();
    res.sendStatus(200);
});

export default router;
