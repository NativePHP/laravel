import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import startAPIServer, { APIProcess } from "../src/server/api";
import axios from "axios";

vi.mock('electron-updater', () => {
    return {
        default: {
            autoUpdater: {
                checkForUpdates: vi.fn(),
                quitAndInstall: vi.fn(),
                addListener: vi.fn(),
                downloadUpdate: vi.fn(),
            },
        },
    };
});

let apiServer: APIProcess;

describe('API test', () => {
    beforeEach(async () => {
        vi.resetModules();
        apiServer = await startAPIServer('randomSecret');
        axios.defaults.baseURL = `http://localhost:${apiServer.port}`;
    });

    afterEach(async () => {
        await new Promise<void>((resolve) => {
            apiServer.server.close(() => resolve());
        });
    });

    it('starts API server on port 4000', async () => {
        // NOTE: If this fails it may be you have a NativePHP app running locally
        // and the port negotiation actually woks as expected (might be 4001).
        // Quit any running NativePHP apps to verify.
        expect(apiServer.port).toBe(4000);
    });

    it('uses the next available API port', async () => {
        const nextApiProcess = await startAPIServer('randomSecret');
        expect(nextApiProcess.port).toBe(apiServer.port + 1);

        nextApiProcess.server.close();
    });

    it('protects API endpoints with a secret', async () => {
        try {
            await axios.get('/api/process');
        } catch (error) {
            expect(error.response.status).toBe(403);
        }

        let response;
        try {
            response = await axios.get('/api/process', {
                headers: {
                    'x-nativephp-secret': 'randomSecret',
                }
            });
        } finally {
            expect(response.status).toBe(200);
        }
    });
});
