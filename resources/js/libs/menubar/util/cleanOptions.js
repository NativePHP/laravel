"use strict";
/**
 * @ignore
 */
var __assign = (this && this.__assign) || function () {
    __assign = Object.assign || function(t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
            s = arguments[i];
            for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p))
                t[p] = s[p];
        }
        return t;
    };
    return __assign.apply(this, arguments);
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.cleanOptions = void 0;
/** */
var path_1 = __importDefault(require("path"));
var url_1 = __importDefault(require("url"));
var electron_1 = require("electron");
var DEFAULT_WINDOW_HEIGHT = 400;
var DEFAULT_WINDOW_WIDTH = 400;
/**
 * Take as input some options, and return a sanitized version of it.
 *
 * @param opts - The options to clean.
 * @ignore
 */
function cleanOptions(opts) {
    var options = __assign({}, opts);
    if (options.activateWithApp === undefined) {
        options.activateWithApp = true;
    }
    if (!options.dir) {
        options.dir = electron_1.app.getAppPath();
    }
    if (!path_1.default.isAbsolute(options.dir)) {
        options.dir = path_1.default.resolve(options.dir);
    }
    // Note: options.index can be `false`
    if (options.index === undefined) {
        options.index = url_1.default.format({
            pathname: path_1.default.join(options.dir, 'index.html'),
            protocol: 'file:',
            slashes: true,
        });
    }
    options.loadUrlOptions = options.loadUrlOptions || {};
    options.tooltip = options.tooltip || '';
    // `icon`, `preloadWindow`, `showDockIcon`, `showOnAllWorkspaces`,
    // `showOnRightClick` don't need any special treatment
    // Now we take care of `browserWindow`
    if (!options.browserWindow) {
        options.browserWindow = {};
    }
    // Set width/height on options to be usable before the window is created
    options.browserWindow.width =
        // Note: not using `options.browserWindow.width || DEFAULT_WINDOW_WIDTH` so
        // that users can put a 0 width
        options.browserWindow.width !== undefined
            ? options.browserWindow.width
            : DEFAULT_WINDOW_WIDTH;
    options.browserWindow.height =
        options.browserWindow.height !== undefined
            ? options.browserWindow.height
            : DEFAULT_WINDOW_HEIGHT;
    return options;
}
exports.cleanOptions = cleanOptions;
