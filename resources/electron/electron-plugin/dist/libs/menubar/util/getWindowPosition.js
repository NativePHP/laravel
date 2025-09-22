import { screen as electronScreen } from 'electron';
const isLinux = process.platform === 'linux';
const trayToScreenRects = (tray) => {
    const { workArea, bounds: screenBounds } = electronScreen.getDisplayMatching(tray.getBounds());
    workArea.x -= screenBounds.x;
    workArea.y -= screenBounds.y;
    return [screenBounds, workArea];
};
export function taskbarLocation(tray) {
    const [screenBounds, workArea] = trayToScreenRects(tray);
    if (workArea.x > 0) {
        if (isLinux && workArea.y > 0)
            return 'top';
        return 'left';
    }
    if (workArea.y > 0) {
        return 'top';
    }
    if (workArea.width < screenBounds.width) {
        return 'right';
    }
    return 'bottom';
}
export function getWindowPosition(tray) {
    switch (process.platform) {
        case 'darwin':
            return 'trayCenter';
        case 'linux':
        case 'win32': {
            const traySide = taskbarLocation(tray);
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
    return 'topRight';
}
