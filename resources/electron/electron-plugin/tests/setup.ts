// vitest-setup.ts

import { vi } from 'vitest';
import { mockForNodeRequire } from "vitest-mock-commonjs"
import express from 'express';

// Mock electron
mockForNodeRequire('electron', () => ({
    app: {
        getPath: vi.fn(),
        on: vi.fn(),
        quit: vi.fn(),
        getName: vi.fn(),
        getVersion: vi.fn(),
        focus: vi.fn(),
        hide: vi.fn(),
        dock: {
            setMenu: vi.fn(),
            show: vi.fn(),
            hide: vi.fn(),
            setBadge: vi.fn(),
            bounce: vi.fn(),
            cancelBounce: vi.fn(),
        },
        isPackaged: false,
        getAppPath: vi.fn().mockReturnValue('/fake/app/path'),
    },
    clipboard: {
        writeText: vi.fn(),
        readText: vi.fn(),
        writeImage: vi.fn(),
        readImage: vi.fn(),
    },
    BrowserWindow: vi.fn().mockImplementation(() => ({
        loadURL: vi.fn(),
        loadFile: vi.fn(),
        on: vi.fn(),
        setMenu: vi.fn(),
        setMenuBarVisibility: vi.fn(),
        webContents: {
            on: vi.fn(),
            send: vi.fn(),
        },
        show: vi.fn(),
        hide: vi.fn(),
        close: vi.fn(),
        maximize: vi.fn(),
        minimize: vi.fn(),
        restore: vi.fn(),
        isMaximized: vi.fn(),
        isMinimized: vi.fn(),
        setProgressBar: vi.fn(),
    })),
    ipcMain: {
        on: vi.fn(),
        handle: vi.fn(),
        removeHandler: vi.fn(),
    },
    screen: {
        getPrimaryDisplay: vi.fn().mockReturnValue({
            id: 1,
            bounds: { x: 0, y: 0, width: 1920, height: 1080 },
            workArea: { x: 0, y: 0, width: 1920, height: 1040 },
            scaleFactor: 1,
            rotation: 0,
        }),
        getAllDisplays: vi.fn().mockReturnValue([{
            id: 1,
            bounds: { x: 0, y: 0, width: 1920, height: 1080 },
            workArea: { x: 0, y: 0, width: 1920, height: 1040 },
            scaleFactor: 1,
            rotation: 0,
        }]),
    },

    dialog: {
        showOpenDialogSync: vi.fn(() => ['open dialog result']),
        showSaveDialogSync: vi.fn(() => ['save dialog result']),
        showMessageBoxSync: vi.fn(() => 1),
        showErrorBox: vi.fn(),
    },
    globalShortcut: {
        register: vi.fn(),
        unregister: vi.fn(),
        isRegistered: vi.fn(),
        unregisterAll: vi.fn(),
    },
    Notification: vi.fn().mockImplementation(() => ({
        show: vi.fn(),
        on: vi.fn(),
    })),
    Menu: {
        buildFromTemplate: vi.fn().mockReturnValue({
            popup: vi.fn(),
            closePopup: vi.fn(),
            append: vi.fn(),
            insert: vi.fn(),
            getMenuItemById: vi.fn(),
        }),
        setApplicationMenu: vi.fn(),
        getApplicationMenu: vi.fn(),
    },
    Tray: vi.fn().mockImplementation(() => ({
        setContextMenu: vi.fn(),
        on: vi.fn(),
        setImage: vi.fn(),
        setToolTip: vi.fn(),
        destroy: vi.fn(),
    })),
    MenuItem: vi.fn(),
    shell: {
        openExternal: vi.fn().mockResolvedValue(true),
        openPath: vi.fn().mockResolvedValue({ success: true }),
        showItemInFolder: vi.fn(),
    },
    powerMonitor: {
        on: vi.fn(),
        addListener: vi.fn(),
        removeAllListeners: vi.fn(),
    },
    nativeTheme: {
        on: vi.fn(),
        shouldUseDarkColors: false,
    },
    safeStorage: {
        encryptString: vi.fn((str) => Buffer.from(str).toString('base64')),
        decryptString: vi.fn((buffer) => Buffer.from(buffer, 'base64').toString()),
    },
}));

// Mock @electron/remote
vi.mock('@electron/remote', () => ({
    initialize: vi.fn(),
    enable: vi.fn(),
    getCurrentWindow: vi.fn().mockReturnValue({
        loadURL: vi.fn(),
        loadFile: vi.fn(),
        on: vi.fn(),
        setMenu: vi.fn(),
        show: vi.fn(),
        hide: vi.fn(),
        close: vi.fn(),
        maximize: vi.fn(),
        minimize: vi.fn(),
        restore: vi.fn(),
        isMaximized: vi.fn(),
        isMinimized: vi.fn(),
        setProgressBar: vi.fn(),
        webContents: {
            on: vi.fn(),
            send: vi.fn(),
        },
    }),
    app: {
        getPath: vi.fn(),
        getName: vi.fn(),
        getVersion: vi.fn(),
        getAppPath: vi.fn().mockReturnValue('/fake/app/path'),
    },
    dialog: {
        showOpenDialog: vi.fn().mockResolvedValue({ filePaths: [] }),
        showSaveDialog: vi.fn().mockResolvedValue({ filePath: '' }),
    },
}));

// Mock electron-store with onDidAnyChange method
vi.mock('electron-store', () => {
    return {
        default: vi.fn().mockImplementation(() => ({
            get: vi.fn(),
            set: vi.fn(),
            has: vi.fn(),
            delete: vi.fn(),
            clear: vi.fn(),
            onDidAnyChange: vi.fn().mockImplementation(callback => {
                // Return an unsubscribe function
                return () => {};
            }),
            store: {},
            path: '/fake/path/to/store.json',
        })),
    };
});

// Create empty router mocks for all API routes
const createRouterMock = () => {
    const router = express.Router();
    return { default: router };
};

// Mock individual route files directly to avoid the @electron/remote issue
// vi.mock('../src/server/api/clipboard.js', () => createRouterMock());
// vi.mock('../src/server/api/app.js', () => createRouterMock());
// vi.mock('../src/server/api/screen.js', () => createRouterMock());
// vi.mock('../src/server/api/dialog.js', () => createRouterMock());
// vi.mock('../src/server/api/debug.js', () => createRouterMock());
// vi.mock('../src/server/api/broadcasting.js', () => createRouterMock());
// vi.mock('../src/server/api/system.js', () => createRouterMock());
// vi.mock('../src/server/api/globalShortcut.js', () => createRouterMock());
// vi.mock('../src/server/api/notification.js', () => createRouterMock());
// vi.mock('../src/server/api/dock.js', () => createRouterMock());
// vi.mock('../src/server/api/menu.js', () => createRouterMock());
vi.mock('../src/server/api/menuBar.js', () => createRouterMock());
vi.mock('../src/server/api/window.js', () => createRouterMock());
// vi.mock('../src/server/api/process.js', () => createRouterMock());
vi.mock('../src/server/api/contextMenu.js', () => createRouterMock());
// vi.mock('../src/server/api/settings.js', () => createRouterMock());
// vi.mock('../src/server/api/shell.js', () => createRouterMock());
// vi.mock('../src/server/api/progressBar.js', () => createRouterMock());
vi.mock('../src/server/api/powerMonitor.js', () => createRouterMock());
vi.mock('../src/server/api/childProcess.js', () => createRouterMock());
