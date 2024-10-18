var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import express from 'express';
import { BrowserWindow, systemPreferences, safeStorage } from 'electron';
const router = express.Router();
router.get('/can-prompt-touch-id', (req, res) => {
    res.json({
        result: systemPreferences.canPromptTouchID(),
    });
});
router.post('/prompt-touch-id', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    try {
        yield systemPreferences.promptTouchID(req.body.reason);
        res.sendStatus(200);
    }
    catch (e) {
        res.status(400).json({
            error: e.message,
        });
    }
}));
router.get('/can-encrypt', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    res.json({
        result: yield safeStorage.isEncryptionAvailable(),
    });
}));
router.post('/encrypt', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    try {
        res.json({
            result: yield safeStorage.encryptString(req.body.string).toString('base64'),
        });
    }
    catch (e) {
        res.status(400).json({
            error: e.message,
        });
    }
}));
router.post('/decrypt', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    try {
        res.json({
            result: yield safeStorage.decryptString(Buffer.from(req.body.string, 'base64')),
        });
    }
    catch (e) {
        res.status(400).json({
            error: e.message,
        });
    }
}));
router.get('/printers', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const printers = yield BrowserWindow.getAllWindows()[0].webContents.getPrintersAsync();
    res.json({
        printers,
    });
}));
router.post('/print', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { printer, html } = req.body;
    let printWindow = new BrowserWindow({
        show: false,
    });
    printWindow.webContents.on('did-finish-load', () => {
        printWindow.webContents.print({
            silent: true,
            deviceName: printer,
        }, (success, errorType) => {
            res.sendStatus(200);
        });
        printWindow = null;
    });
    yield printWindow.loadURL(`data:text/html;charset=UTF-8,${html}`);
}));
router.post('/print-to-pdf', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { html } = req.body;
    let printWindow = new BrowserWindow({
        show: false,
    });
    printWindow.webContents.on('did-finish-load', () => {
        printWindow.webContents.printToPDF({}).then(data => {
            printWindow.close();
            res.json({
                result: data.toString('base64'),
            });
        }).catch(e => {
            printWindow.close();
            res.status(400).json({
                error: e.message,
            });
        });
    });
    yield printWindow.loadURL(`data:text/html;charset=UTF-8,${html}`);
}));
export default router;
