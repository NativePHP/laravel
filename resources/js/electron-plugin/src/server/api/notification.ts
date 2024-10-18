import express from 'express'
import { Notification } from 'electron'
import {notifyLaravel} from "../utils";
const router = express.Router();

router.post('/', (req, res) => {
    const {title, body, subtitle, silent, icon, hasReply, timeoutType, replyPlaceholder, sound, urgency, actions, closeButtonText, toastXml} = req.body

    const notification = new Notification({title, body, subtitle, silent, icon, hasReply, timeoutType, replyPlaceholder, sound, urgency, actions, closeButtonText, toastXml});

    notification.on("click", (event)=>{
        notifyLaravel('events', {
            event: '\\Native\\Laravel\\Events\\Notifications\\NotificationClicked',
            payload: []
        })
    })

    notification.show()

    res.sendStatus(200)
});

export default router;
