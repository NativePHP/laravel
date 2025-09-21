var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
import { EventEmitter } from 'events';
import fs from 'fs';
import path from 'path';
import { BrowserWindow, Tray } from 'electron';
import Positioner from '../positioner/index.js';
import { cleanOptions } from './util/cleanOptions.js';
import { getWindowPosition } from './util/getWindowPosition.js';
export class Menubar extends EventEmitter {
    constructor(app, options) {
        super();
        this._blurTimeout = null;
        this._app = app;
        this._options = cleanOptions(options);
        this._isVisible = false;
        if (app.isReady()) {
            process.nextTick(() => this.appReady().catch((err) => console.error('menubar: ', err)));
        }
        else {
            app.on('ready', () => {
                this.appReady().catch((err) => console.error('menubar: ', err));
            });
        }
    }
    get app() {
        return this._app;
    }
    get positioner() {
        if (!this._positioner) {
            throw new Error('Please access `this.positioner` after the `after-create-window` event has fired.');
        }
        return this._positioner;
    }
    get tray() {
        if (!this._tray) {
            throw new Error('Please access `this.tray` after the `ready` event has fired.');
        }
        return this._tray;
    }
    get window() {
        return this._browserWindow;
    }
    getOption(key) {
        return this._options[key];
    }
    hideWindow() {
        if (!this._browserWindow || !this._isVisible) {
            return;
        }
        this.emit('hide');
        this._browserWindow.hide();
        this.emit('after-hide');
        this._isVisible = false;
        if (this._blurTimeout) {
            clearTimeout(this._blurTimeout);
            this._blurTimeout = null;
        }
    }
    setOption(key, value) {
        this._options[key] = value;
    }
    showWindow(trayPos) {
        return __awaiter(this, void 0, void 0, function* () {
            if (!this.tray) {
                throw new Error('Tray should have been instantiated by now');
            }
            if (!this._browserWindow) {
                yield this.createWindow();
            }
            if (!this._browserWindow) {
                throw new Error('Window has been initialized just above. qed.');
            }
            if (['win32', 'linux'].includes(process.platform)) {
                this._options.windowPosition = getWindowPosition(this.tray);
            }
            this.emit('show');
            if (trayPos && trayPos.x !== 0) {
                this._cachedBounds = trayPos;
            }
            else if (this._cachedBounds) {
                trayPos = this._cachedBounds;
            }
            else if (this.tray.getBounds) {
                trayPos = this.tray.getBounds();
            }
            let noBoundsPosition = undefined;
            if ((trayPos === undefined || trayPos.x === 0) &&
                this._options.windowPosition &&
                this._options.windowPosition.startsWith('tray')) {
                noBoundsPosition =
                    process.platform === 'win32' ? 'bottomRight' : 'topRight';
            }
            const position = this.positioner.calculate(this._options.windowPosition || noBoundsPosition, trayPos);
            const x = this._options.browserWindow.x !== undefined
                ? this._options.browserWindow.x
                : position.x;
            const y = this._options.browserWindow.y !== undefined
                ? this._options.browserWindow.y
                : position.y;
            this._browserWindow.setPosition(Math.round(x), Math.round(y));
            this._browserWindow.show();
            this._isVisible = true;
            this.emit('after-show');
            return;
        });
    }
    appReady() {
        return __awaiter(this, void 0, void 0, function* () {
            if (this.app.dock && !this._options.showDockIcon) {
                this.app.dock.hide();
            }
            if (this._options.activateWithApp) {
                this.app.on('activate', (_event, hasVisibleWindows) => {
                    if (!hasVisibleWindows) {
                        this.showWindow().catch(console.error);
                    }
                });
            }
            let trayImage = this._options.icon || path.join(this._options.dir, 'IconTemplate.png');
            if (typeof trayImage === 'string' && !fs.existsSync(trayImage)) {
                trayImage = path.join(__dirname, '..', 'assets', 'IconTemplate.png');
            }
            const defaultClickEvent = this._options.showOnRightClick
                ? 'right-click'
                : 'click';
            this._tray = this._options.tray || new Tray(trayImage);
            if (!this.tray) {
                throw new Error('Tray has been initialized above');
            }
            this.tray.on(defaultClickEvent, this.clicked.bind(this));
            this.tray.on('double-click', this.clicked.bind(this));
            this.tray.setToolTip(this._options.tooltip);
            if (!this._options.windowPosition) {
                this._options.windowPosition = getWindowPosition(this.tray);
            }
            if (this._options.preloadWindow) {
                yield this.createWindow();
            }
            this.emit('ready');
        });
    }
    clicked(event, bounds) {
        return __awaiter(this, void 0, void 0, function* () {
            if (event && (event.shiftKey || event.ctrlKey || event.metaKey)) {
                return this.hideWindow();
            }
            if (this._blurTimeout) {
                clearInterval(this._blurTimeout);
            }
            if (this._browserWindow && this._isVisible) {
                return this.hideWindow();
            }
            this._cachedBounds = bounds || this._cachedBounds;
            yield this.showWindow(this._cachedBounds);
        });
    }
    createWindow() {
        return __awaiter(this, void 0, void 0, function* () {
            this.emit('create-window');
            const defaults = {
                show: false,
                frame: false,
            };
            this._browserWindow = new BrowserWindow(Object.assign(Object.assign({}, defaults), this._options.browserWindow));
            this._positioner = new Positioner(this._browserWindow);
            this._browserWindow.on('blur', () => {
                if (!this._browserWindow) {
                    return;
                }
                this._browserWindow.isAlwaysOnTop()
                    ? this.emit('focus-lost')
                    : (this._blurTimeout = setTimeout(() => {
                        this.hideWindow();
                    }, 100));
            });
            if (this._options.showOnAllWorkspaces !== false) {
                this._browserWindow.setVisibleOnAllWorkspaces(true, {
                    skipTransformProcessType: true,
                });
            }
            this._browserWindow.on('close', this.windowClear.bind(this));
            this.emit('before-load');
            if (this._options.index !== false) {
                yield this._browserWindow.loadURL(this._options.index, this._options.loadUrlOptions);
            }
            this.emit('after-create-window');
        });
    }
    windowClear() {
        this._browserWindow = undefined;
        this.emit('after-close');
    }
}
