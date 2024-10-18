import express from 'express'
import {dialog} from 'electron'
import state from '../state'
import {trimOptions} from '../utils'
const router = express.Router();

router.post('/open', (req, res) => {
    const {title, buttonLabel, filters, properties, defaultPath, message, windowReference} = req.body

    let options = {
        title,
        defaultPath,
        buttonLabel,
        filters,
        message,
        properties
    };

    options = trimOptions(options);

    let result;
    let browserWindow = state.findWindow(windowReference);

    if (browserWindow) {
        result = dialog.showOpenDialogSync(browserWindow, options)
    } else {
        result = dialog.showOpenDialogSync(options)
    }

    res.json({
        result
    })
});

router.post('/save', (req, res) => {
    const {title, buttonLabel, filters, properties, defaultPath, message, windowReference} = req.body

    let options = {
        title,
        defaultPath,
        buttonLabel,
        filters,
        message,
        properties
    };

    options = trimOptions(options);

    let result;
    let browserWindow = state.findWindow(windowReference);

    if (browserWindow) {
      result = dialog.showSaveDialogSync(browserWindow, options)
    } else {
      result = dialog.showSaveDialogSync(options)
    }

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
    const {title, message} = req.body

    dialog.showErrorBox(title, message)

    res.json({
        result: true
    })
});

export default router;
