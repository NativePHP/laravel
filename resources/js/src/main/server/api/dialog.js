import express from 'express'
import {dialog} from 'electron'
const router = express.Router();

function trimOptions(options) {
    Object.keys(options).forEach(key => options[key] == null && delete options[key]);

    return options;
}

router.post('/open', (req, res) => {
    const {title, buttonLabel, filters, properties, defaultPath, message} = req.body

    let options = {
        title,
        defaultPath,
        buttonLabel,
        filters,
        message,
        properties
    };

    options = trimOptions(options);

    const result = dialog.showOpenDialogSync(options)

    res.json({
        result
    })
});

router.post('/save', (req, res) => {
    const {title, buttonLabel, filters, properties, defaultPath, message} = req.body

    let options = {
        title,
        defaultPath,
        buttonLabel,
        filters,
        message,
        properties
    };

    options = trimOptions(options);

    const result = dialog.showSaveDialogSync(options)

    res.json({
        result
    })
});

router.post('/message', (req, res) => {
    const {title, message, type, buttons} = req.body

    const result = dialog.showMessageBoxSync({
        title,
        message,
        type,
        buttons
    })

    res.json({
        result
    })
});

router.post('/error', (req, res) => {
    const {title, message, detail} = req.body

    dialog.showErrorBox(title, message, detail)

    res.json({
        result: true
    })
});

export default router;
