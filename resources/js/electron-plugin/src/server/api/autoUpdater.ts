import express from "express";
import { autoUpdater } from "electron-updater";
import type { ProgressInfo, UpdateDownloadedEvent } from "electron-updater";
import { notifyLaravel } from "../utils.js";

const router = express.Router();

router.post("/check-for-updates", (req, res) => {
    autoUpdater.checkForUpdates();
    res.sendStatus(200);
});

router.post("/quit-and-install", (req, res) => {
    autoUpdater.quitAndInstall();
    res.sendStatus(200);
});

autoUpdater.addListener("checking-for-update", () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\AutoUpdater\\CheckingForUpdate`,
    });
});

autoUpdater.addListener("update-available", () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\AutoUpdater\\UpdateAvailable`,
    });
});

autoUpdater.addListener("update-not-available", () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\AutoUpdater\\UpdateNotAvailable`,
    });
});

autoUpdater.addListener("error", (error) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\AutoUpdater\\Error`,
        payload: {
            error: error,
        },
    });
});

autoUpdater.addListener("download-progress", (progressInfo: ProgressInfo) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\AutoUpdater\\DownloadProgress`,
        payload: {
            total: progressInfo.total,
            delta: progressInfo.delta,
            transferred: progressInfo.transferred,
            percent: progressInfo.percent,
            bytesPerSecond: progressInfo.bytesPerSecond,
        },
    });
});

autoUpdater.addListener("update-downloaded", (event: UpdateDownloadedEvent) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\AutoUpdater\\UpdateDownloaded`,
        payload: {
            version: event.version,
            downloadedFile: event.downloadedFile,
            releaseDate: event.releaseDate,
            releaseNotes: event.releaseNotes,
            releaseName: event.releaseName,
        },
    });
});

export default router;
