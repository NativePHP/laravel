import { app } from 'electron';
import { Menubar } from './Menubar.js';
export * from './util/getWindowPosition.js';
export { Menubar };
export function menubar(options) {
    return new Menubar(app, options);
}
