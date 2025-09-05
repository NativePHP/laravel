"use strict";
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
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || function (mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
    __setModuleDefault(result, mod);
    return result;
};
Object.defineProperty(exports, "__esModule", { value: true });
var path = __importStar(require("path"));
var electron_1 = require("../__mocks__/electron");
var cleanOptions_1 = require("./cleanOptions");
var DEFAULT_OPTIONS = {
    activateWithApp: true,
    browserWindow: {
        height: 400,
        width: 400,
    },
    dir: path.resolve(electron_1.MOCK_APP_GETAPPPATH),
    index: "file://".concat(path.join(path.resolve(electron_1.MOCK_APP_GETAPPPATH), 'index.html')),
    loadUrlOptions: {},
    tooltip: '',
};
describe('cleanOptions', function () {
    it('should handle undefined', function () {
        expect((0, cleanOptions_1.cleanOptions)(undefined)).toEqual(DEFAULT_OPTIONS);
    });
    it('should handle a dir string with relative path', function () {
        expect((0, cleanOptions_1.cleanOptions)({ dir: 'MY_RELATIVE_PATH' })).toEqual(__assign(__assign({}, DEFAULT_OPTIONS), { dir: path.resolve('MY_RELATIVE_PATH'), index: "file://".concat(path.join(path.resolve('MY_RELATIVE_PATH'), 'index.html')) }));
    });
    it('should handle a dir string with absolute path', function () {
        expect((0, cleanOptions_1.cleanOptions)({ dir: '/home/me/MY_ABSOLUTE_PATH' })).toEqual(__assign(__assign({}, DEFAULT_OPTIONS), { dir: '/home/me/MY_ABSOLUTE_PATH', index: 'file:///home/me/MY_ABSOLUTE_PATH/index.html' }));
    });
    it('should handle a false index', function () {
        expect((0, cleanOptions_1.cleanOptions)({ index: false })).toEqual(__assign(__assign({}, DEFAULT_OPTIONS), { index: false }));
    });
    it('should handle an object with multiple fields', function () {
        expect((0, cleanOptions_1.cleanOptions)({
            browserWindow: {
                height: 100,
            },
            index: 'file:///home/abc/index.html',
            showDockIcon: true,
            windowPosition: 'trayCenter',
        })).toEqual(__assign(__assign({}, DEFAULT_OPTIONS), { browserWindow: __assign(__assign({}, DEFAULT_OPTIONS.browserWindow), { height: 100 }), index: 'file:///home/abc/index.html', showDockIcon: true, windowPosition: 'trayCenter' }));
    });
});
