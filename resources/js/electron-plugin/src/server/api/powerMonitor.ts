import express from 'express'
import { powerMonitor } from 'electron'
import { notifyLaravel } from '../utils';
const router = express.Router();

router.get('/get-system-idle-state', (req, res) => {
    res.json({
        result: powerMonitor.getSystemIdleState(req.body.threshold),
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

powerMonitor.addListener('thermal-state-change', (state: string) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\ThermalStateChanged`,
        payload: {
            state
        }
    });
})

powerMonitor.addListener('speed-limit-change', (limit: number) => {
    notifyLaravel("events", {
        event: `\\Native\\Laravel\\Events\\PowerMonitor\\SpeedLimitChanged`,
        payload: {
            limit
        }
    });
})

export default router;
