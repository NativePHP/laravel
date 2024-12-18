import express from 'express'
import { powerMonitor } from 'electron'
import { notifyLaravel } from '../utils';
const router = express.Router();

router.get('/get-system-idle-state', (req, res) => {
    let threshold = Number(req.query.threshold) || 60;

    res.json({
        result: powerMonitor.getSystemIdleState(threshold),
    })
});

router.get('/get-system-idle-time', (req, res) => {
    res.json({
        result: powerMonitor.getSystemIdleTime(),
    })
});

router.get('/get-current-thermal-state', (req, res) => {
    res.json({
        result: powerMonitor.getCurrentThermalState(),
    })
});

router.get('/is-on-battery-power', (req, res) => {
    res.json({
        result: powerMonitor.isOnBatteryPower(),
    })
});

powerMonitor.addListener('on-ac', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\PowerStateChanged`,
        payload: {
            state: 'on-ac'
        }
    });
})

powerMonitor.addListener('on-battery', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\PowerStateChanged`,
        payload: {
            state: 'on-battery'
        }
    });
})

// @ts-ignore
powerMonitor.addListener('thermal-state-change', (details) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\ThermalStateChanged`,
        payload: {
            state: details.state,
        },
    });
})

// @ts-ignore
powerMonitor.addListener('speed-limit-change', (details) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\SpeedLimitChanged`,
        payload: {
            limit: details.limit,
        },
    });
})

// @ts-ignore
powerMonitor.addListener('lock-screen', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\ScreenLocked`,
    });
})

// @ts-ignore
powerMonitor.addListener('unlock-screen', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\ScreenUnlocked`,
    });
})


// @ts-ignore
powerMonitor.addListener('shutdown', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\Shutdown`,
    });
})


// @ts-ignore
powerMonitor.addListener('user-did-become-active', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\UserDidBecomeActive`,
    });
})


// @ts-ignore
powerMonitor.addListener('user-did-resign-active', () => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\UserDidResignActive`,
    });
})

export default router;
