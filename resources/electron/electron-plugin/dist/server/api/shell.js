var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import express from "express";
import { shell } from "electron";
const router = express.Router();
router.post("/show-item-in-folder", (req, res) => {
    const { path } = req.body;
    shell.showItemInFolder(path);
    res.sendStatus(200);
});
router.post("/open-item", (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { path } = req.body;
    let result = yield shell.openPath(path);
    res.json({
        result
    });
}));
router.post("/open-external", (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { url } = req.body;
    try {
        yield shell.openExternal(url);
        res.sendStatus(200);
    }
    catch (e) {
        res.status(500).json({
            error: e
        });
    }
}));
router.delete("/trash-item", (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { path } = req.body;
    try {
        yield shell.trashItem(path);
        res.sendStatus(200);
    }
    catch (e) {
        res.status(400).json();
    }
}));
export default router;
