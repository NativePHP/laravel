import { vi } from 'vitest';

const electron = {
    app: {
        getPath: vi.fn().mockReturnValue('path'),
        isPackaged: false,
    },
    powerMonitor: {
        addListener: vi.fn(),
    },
};

// Make sure this is an object with properties
Object.defineProperty(electron, '__esModule', { value: true });
export default electron;
export const app = electron.app;
export const powerMonitor = electron.powerMonitor;
