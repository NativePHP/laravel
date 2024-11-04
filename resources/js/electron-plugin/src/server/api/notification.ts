import express from 'express';
import { Notification } from 'electron';
import {notifyLaravel} from "../utils";
const router = express.Router();

router.post('/', (req, res) => {
    const {
        title,
        body,
        subtitle,
        silent,
        icon,
        hasReply,
        timeoutType,
        replyPlaceholder,
        sound,
        urgency,
        actions,
        closeButtonText,
        toastXml,
        event: customEvent
    } = req.body;

    const eventName = customEvent ?? '\\Native\\Laravel\\Events\\Notifications\\NotificationClicked';

    const notification = new Notification({
        title,
        body,
        subtitle,
        silent,
        icon,
        hasReply,
        timeoutType,
        replyPlaceholder,
        sound,
        urgency,
        actions,
        closeButtonText,
        toastXml
    });

    notification.on("click", (event) => {
        notifyLaravel('events', {
            event: eventName,
            payload: JSON.stringify(event)
        });
    });

    notification.show();

    res.sendStatus(200);
});

export default router;
