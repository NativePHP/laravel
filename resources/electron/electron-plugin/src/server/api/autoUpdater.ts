import express from "express";
import electronUpdater from 'electron-updater';
const { autoUpdater } = electronUpdater;
import type {
    ProgressInfo,
    UpdateDownloadedEvent,
    UpdateInfo,
} from "electron-updater";
import { notifyLaravel } from "../utils.js";

const router = express.Router();

router.post("/check-for-updates", (req, res) => {
    autoUpdater.checkForUpdates();
    res.sendStatus(200);
});

router.post("/download-update", (req, res) => {
    autoUpdater.downloadUpdate();
    res.sendStatus(200);
});

router.post("/quit-and-install", (req, res) => {
    autoUpdater.quitAndInstall();
    res.sendStatus(200);
});

autoUpdater.addListener("checking-for-update", () => {
    notifyLaravel("events", {
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\CheckingForUpdate`,
    });
});

autoUpdater.addListener("update-available", (event: UpdateInfo) => {
    notifyLaravel("events", {
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\UpdateAvailable`,
        payload: {
            version: event.version,
            files: event.files,
            releaseDate: event.releaseDate,
            releaseName: event.releaseName,
            releaseNotes: event.releaseNotes,
            stagingPercentage: event.stagingPercentage,
            minimumSystemVersion: event.minimumSystemVersion,
        },
    });
});

autoUpdater.addListener("update-not-available", (event: UpdateInfo) => {
    notifyLaravel("events", {
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\UpdateNotAvailable`,
        payload: {
            version: event.version,
            files: event.files,
            releaseDate: event.releaseDate,
            releaseName: event.releaseName,
            releaseNotes: event.releaseNotes,
            stagingPercentage: event.stagingPercentage,
            minimumSystemVersion: event.minimumSystemVersion,
        },
    });
});

autoUpdater.addListener("error", (error: Error) => {
    notifyLaravel("events", {
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\Error`,
        payload: {
            name: error.name,
            message: error.message,
            stack: error.stack,
        },
    });
});

autoUpdater.addListener("download-progress", (progressInfo: ProgressInfo) => {
    notifyLaravel("events", {
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\DownloadProgress`,
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
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\UpdateDownloaded`,
        payload: {
            downloadedFile: event.downloadedFile,
            version: event.version,
            files: event.files,
            releaseDate: event.releaseDate,
            releaseName: event.releaseName,
            releaseNotes: event.releaseNotes,
            stagingPercentage: event.stagingPercentage,
            minimumSystemVersion: event.minimumSystemVersion,
        },
    });
});

autoUpdater.addListener("update-cancelled", (event: UpdateInfo) => {
    notifyLaravel("events", {
        event: `\\Native\\Desktop\\Events\\AutoUpdater\\UpdateCancelled`,
        payload: {
            version: event.version,
            files: event.files,
            releaseDate: event.releaseDate,
            releaseName: event.releaseName,
            releaseNotes: event.releaseNotes,
            stagingPercentage: event.stagingPercentage,
            minimumSystemVersion: event.minimumSystemVersion,
        },
    });
});

export default router;
