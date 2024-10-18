import express from 'express'
import {app, Menu} from 'electron'
import {mapMenu} from "./helper";
const router = express.Router();

router.get('/', (req, res) => {
    res.json({
        pid: process.pid,
        platform: process.platform,
        arch: process.arch,
        uptime: process.uptime()
    })
})

export default router;
