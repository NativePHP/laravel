import express from 'express';
import { Notification } from 'electron';
import { notifyLaravel } from "../utils.js";
const router = express.Router();
router.post('/', (req, res) => {
    const { title, body, subtitle, silent, icon, hasReply, timeoutType, replyPlaceholder, sound, urgency, actions, closeButtonText, toastXml, event: customEvent, reference, } = req.body;
    const eventName = customEvent !== null && customEvent !== void 0 ? customEvent : '\\Native\\Laravel\\Events\\Notifications\\NotificationClicked';
    const notificationReference = reference !== null && reference !== void 0 ? reference : (Date.now() + '.' + Math.random().toString(36).slice(2, 9));
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
            event: eventName || '\\Native\\Laravel\\Events\\Notifications\\NotificationClicked',
            payload: {
                reference: notificationReference,
                event: JSON.stringify(event),
            },
        });
    });
    notification.on("action", (event, index) => {
        notifyLaravel('events', {
            event: '\\Native\\Laravel\\Events\\Notifications\\NotificationActionClicked',
            payload: {
                reference: notificationReference,
                index,
                event: JSON.stringify(event),
            },
        });
    });
    notification.on("reply", (event, reply) => {
        notifyLaravel('events', {
            event: '\\Native\\Laravel\\Events\\Notifications\\NotificationReply',
            payload: {
                reference: notificationReference,
                reply,
                event: JSON.stringify(event),
            },
        });
    });
    notification.on("close", (event) => {
        notifyLaravel('events', {
            event: '\\Native\\Laravel\\Events\\Notifications\\NotificationClosed',
            payload: {
                reference: notificationReference,
                event: JSON.stringify(event),
            },
        });
    });
    notification.show();
    res.status(200).json({
        reference: notificationReference,
    });
});
export default router;
