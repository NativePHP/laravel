import express from 'express'
import { Notification } from 'electron'
import {notifyLaravel} from "../index";
const router = express.Router();

router.post('/', (req, res) => {
    const {title, body} = req.body

    const notification = new Notification({title, body});

    notification.on('click', (event, arg)=>{
        notifyLaravel('events', {
            event: '\\Native\\Laravel\\Events\\Notifications\\NotificationClicked',
            payload: []
        })
    })

    notification.show()

    res.sendStatus(200)
});

export default router;
