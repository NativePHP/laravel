import { describe, it, expect, vi } from 'vitest';
import {mockForNodeRequire} from "vitest-mock-commonjs";

vi.mock('electron');

import electron, { app, powerMonitor } from 'electron'; // Import the module you want to test

describe('My Electron Module Tests', () => {
    // it('should return an object for electron', () => {
    //     expect(typeof electron).toBe('object');
    // });

    it('should return the correct path', () => {
        const path = app.getPath('user'); // Call the mocked method
        expect(path).toBe('path'); // Assert the return value
    });

    it('should check if the app is packed', async () => {
        const isPackaged =  app.isPackaged; // Call the mocked method
        expect(isPackaged).toBe(false); // Assert the return value
    });

    it('should add a listener to power monitor', () => {
        const callback = vi.fn();
        powerMonitor.addListener('some-event', callback); // Call the mocked method

        // Verify that addListener was called with the correct parameters
        expect(powerMonitor.addListener).toHaveBeenCalledWith('some-event', callback);
    });
});
