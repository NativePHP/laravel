"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var electron_1 = require("electron");
var Menubar_1 = require("./Menubar");
describe('Menubar', function () {
    var mb;
    beforeEach(function () {
        mb = new Menubar_1.Menubar(electron_1.app, { preloadWindow: true });
    });
    it('should have property `app`', function () {
        expect(mb.app).toBeDefined();
    });
    it('should have property `positioner`', function () {
        expect(function () { return mb.positioner; }).toThrow();
        return new Promise(function (resolve) {
            mb.on('after-create-window', function () {
                expect(mb.positioner).toBeDefined();
                resolve();
            });
        });
    });
    it('should have property `tray`', function () {
        expect(function () { return mb.tray; }).toThrow();
        return new Promise(function (resolve) {
            mb.on('ready', function () {
                expect(mb.tray).toBeInstanceOf(electron_1.Tray);
                resolve();
            });
        });
    });
    it('should have property `window`', function () {
        expect(mb.window).toBeUndefined();
        return new Promise(function (resolve) {
            mb.on('ready', function () {
                expect(mb.window).toBeInstanceOf(electron_1.BrowserWindow);
                resolve();
            });
        });
    });
});
