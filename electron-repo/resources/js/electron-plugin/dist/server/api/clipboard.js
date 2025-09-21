import * as express from 'express';
const router = express.Router();
import { clipboard, nativeImage } from 'electron';
const DEFAULT_TYPE = 'clipboard';
router.get('/text', (req, res) => {
    const { type } = req.query;
    res.json({
        text: clipboard.readText(type || DEFAULT_TYPE)
    });
});
router.post('/text', (req, res) => {
    const { text } = req.body;
    const { type } = req.query;
    clipboard.writeText(text, type || DEFAULT_TYPE);
    res.json({
        text,
    });
});
router.get('/html', (req, res) => {
    const { type } = req.query;
    res.json({
        html: clipboard.readHTML(type || DEFAULT_TYPE)
    });
});
router.post('/html', (req, res) => {
    const { html } = req.body;
    const { type } = req.query;
    clipboard.writeHTML(html, type || DEFAULT_TYPE);
    res.json({
        html,
    });
});
router.get('/image', (req, res) => {
    const { type } = req.query;
    const image = clipboard.readImage(type || DEFAULT_TYPE);
    res.json({
        image: image.isEmpty() ? null : image.toDataURL()
    });
});
router.post('/image', (req, res) => {
    const { image } = req.body;
    const { type } = req.query;
    try {
        const _nativeImage = nativeImage.createFromDataURL(image);
        clipboard.writeImage(_nativeImage, type || DEFAULT_TYPE);
    }
    catch (e) {
        res.status(400).json({
            error: e.message,
        });
        return;
    }
    res.sendStatus(200);
});
router.delete('/', (req, res) => {
    const { type } = req.query;
    clipboard.clear(type || DEFAULT_TYPE);
    res.sendStatus(200);
});
export default router;
