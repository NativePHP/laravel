/// <reference types="node" />
import { EventEmitter } from 'events';
import { BrowserWindow, Tray } from 'electron';
import Positioner from '../positioner/index.js';
import type { Options } from './types.js';
/**
 * The main Menubar class.
 *
 * @noInheritDoc
 */
export declare class Menubar extends EventEmitter {
    private _app;
    private _browserWindow?;
    private _blurTimeout;
    private _isVisible;
    private _cachedBounds?;
    private _options;
    private _positioner;
    private _tray?;
    constructor(app: Electron.App, options?: Partial<Options>);
    /**
     * The Electron [App](https://electronjs.org/docs/api/app)
     * instance.
     */
    get app(): Electron.App;
    /**
     * The [electron-positioner](https://github.com/jenslind/electron-positioner)
     * instance.
     */
    get positioner(): Positioner;
    /**
     * The Electron [Tray](https://electronjs.org/docs/api/tray) instance.
     */
    get tray(): Tray;
    /**
     * The Electron [BrowserWindow](https://electronjs.org/docs/api/browser-window)
     * instance, if it's present.
     */
    get window(): BrowserWindow | undefined;
    /**
     * Retrieve a menubar option.
     *
     * @param key - The option key to retrieve, see {@link Options}.
     */
    getOption<K extends keyof Options>(key: K): Options[K];
    /**
     * Hide the menubar window.
     */
    hideWindow(): void;
    /**
     * Change an option after menubar is created.
     *
     * @param key - The option key to modify, see {@link Options}.
     * @param value - The value to set.
     */
    setOption<K extends keyof Options>(key: K, value: Options[K]): void;
    /**
     * Show the menubar window.
     *
     * @param trayPos - The bounds to show the window in.
     */
    showWindow(trayPos?: Electron.Rectangle): Promise<void>;
    private appReady;
    /**
     * Callback on tray icon click or double-click.
     *
     * @param e
     * @param bounds
     */
    private clicked;
    private createWindow;
    private windowClear;
}
