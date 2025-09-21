import express from "express";
import { shell } from "electron";

const router = express.Router();

router.post("/show-item-in-folder", (req, res) => {
  const { path } = req.body;

  shell.showItemInFolder(path);

  res.sendStatus(200);
});

router.post("/open-item", async (req, res) => {
  const { path } = req.body;

  let result = await shell.openPath(path);

  res.json({
    result
  })
});

router.post("/open-external", async (req, res) => {
  const { url } = req.body;

  try {
    await shell.openExternal(url);

    res.sendStatus(200);
  } catch (e) {
    res.status(500).json({
      error: e
    });
  }
});

router.delete("/trash-item", async (req, res) => {
  const { path } = req.body;

  try {
    await shell.trashItem(path);

    res.sendStatus(200);
  } catch (e) {
    res.status(400).json();
  }
});

export default router;
