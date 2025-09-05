"use strict";
/**
 * Utilities to get taskbar position and consequently menubar's position
 */
Object.defineProperty(exports, "__esModule", { value: true });
exports.getWindowPosition = exports.taskbarLocation = void 0;
/** */
var electron_1 = require("electron");
var isLinux = process.platform === 'linux';
var trayToScreenRects = function (tray) {
    // There may be more than one screen, so we need to figure out on which screen our tray icon lives.
    var _a = electron_1.screen.getDisplayMatching(tray.getBounds()), workArea = _a.workArea, screenBounds = _a.bounds;
    workArea.x -= screenBounds.x;
    workArea.y -= screenBounds.y;
    return [screenBounds, workArea];
};
/**
 * Determine taskbard location: "top", "bottom", "left" or "right".
 *
 * Only tested on Windows for now, and only used in Windows.
 *
 * @param tray - The Electron Tray instance.
 */
function taskbarLocation(tray) {
    var _a = trayToScreenRects(tray), screenBounds = _a[0], workArea = _a[1];
    // TASKBAR LEFT
    if (workArea.x > 0) {
        // Most likely Ubuntu hence assuming the window should be on top
        if (isLinux && workArea.y > 0)
            return 'top';
        // The workspace starts more on the right
        return 'left';
    }
    // TASKBAR TOP
    if (workArea.y > 0) {
        return 'top';
    }
    // TASKBAR RIGHT
    // Here both workArea.y and workArea.x are 0 so we can no longer leverage them.
    // We can use the workarea and display width though.
    // Determine taskbar location
    if (workArea.width < screenBounds.width) {
        // The taskbar is either on the left or right, but since the LEFT case was handled above,
        // we can be sure we're dealing with a right taskbar
        return 'right';
    }
    // TASKBAR BOTTOM
    // Since all the other cases were handled, we can be sure we're dealing with a bottom taskbar
    return 'bottom';
}
exports.taskbarLocation = taskbarLocation;
/**
 * Depending on where the taskbar is, determine where the window should be
 * positioned.
 *
 * @param tray - The Electron Tray instance.
 */
function getWindowPosition(tray) {
    switch (process.platform) {
        // macOS
        // Supports top taskbars
        case 'darwin':
            return 'trayCenter';
        // Linux
        // Windows
        // Supports top/bottom/left/right taskbar
        case 'linux':
        case 'win32': {
            var traySide = taskbarLocation(tray);
            // Assign position for menubar
            if (traySide === 'top') {
                return isLinux ? 'topRight' : 'trayCenter';
            }
            if (traySide === 'bottom') {
                return 'bottomRight';
            }
            if (traySide === 'left') {
                return 'bottomLeft';
            }
            if (traySide === 'right') {
                return 'bottomRight';
            }
        }
    }
    // When we really don't know, we just show the menubar on the top-right
    return 'topRight';
}
exports.getWindowPosition = getWindowPosition;
