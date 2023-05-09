import express from 'express'
import { clipboard, NativeImage } from 'electron'
const router = express.Router();

const DEFAULT_TYPE = 'clipboard'

router.get('/text', (req, res) => {
    const {type} = req.query

    res.json({
        text: clipboard.readText(type || DEFAULT_TYPE)
    })
});

router.post('/text', (req, res) => {
    const {text} = req.body
    const {type} = req.query

    clipboard.writeText(text, type || DEFAULT_TYPE)

    res.json({
        text,
    })
});

router.get('/html', (req, res) => {
    const {type} = req.query

    res.json({
        html: clipboard.readHTML(type || DEFAULT_TYPE)
    })
});

router.post('/html', (req, res) => {
    const {html} = req.body
    const {type} = req.query

    clipboard.writeHTML(html, type || DEFAULT_TYPE)

    res.sendStatus(200);
});

router.get('/image', (req, res) => {
    const {type} = req.query
    const image = clipboard.readImage(type || DEFAULT_TYPE);

    res.json({
        image: image.isEmpty() ? null : image.toDataURL()
    })
});

router.post('/image', (req, res) => {
    const {image} = req.body
    const {type} = req.query

    const nativeImage = NativeImage.createFromDataURL(image)
    clipboard.writeImage(image, type || DEFAULT_TYPE)

    res.sendStatus(200);
});

router.delete('/', (req, res) => {
    const {type} = req.query

    clipboard.clear(type || DEFAULT_TYPE)

    res.sendStatus(200);
});

export default router;
