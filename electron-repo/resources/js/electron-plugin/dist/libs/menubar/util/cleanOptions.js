import path from 'path';
import url from 'url';
import { app } from 'electron';
const DEFAULT_WINDOW_HEIGHT = 400;
const DEFAULT_WINDOW_WIDTH = 400;
export function cleanOptions(opts) {
    const options = Object.assign({}, opts);
    if (options.activateWithApp === undefined) {
        options.activateWithApp = true;
    }
    if (!options.dir) {
        options.dir = app.getAppPath();
    }
    if (!path.isAbsolute(options.dir)) {
        options.dir = path.resolve(options.dir);
    }
    if (options.index === undefined) {
        options.index = url.format({
            pathname: path.join(options.dir, 'index.html'),
            protocol: 'file:',
            slashes: true,
        });
    }
    options.loadUrlOptions = options.loadUrlOptions || {};
    options.tooltip = options.tooltip || '';
    if (!options.browserWindow) {
        options.browserWindow = {};
    }
    options.browserWindow.width =
        options.browserWindow.width !== undefined
            ? options.browserWindow.width
            : DEFAULT_WINDOW_WIDTH;
    options.browserWindow.height =
        options.browserWindow.height !== undefined
            ? options.browserWindow.height
            : DEFAULT_WINDOW_HEIGHT;
    return options;
}
