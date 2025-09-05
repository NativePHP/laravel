/**
 * Utilities to get taskbar position and consequently menubar's position
 */
/** */
import { type Tray } from 'electron';
type TaskbarLocation = 'top' | 'bottom' | 'left' | 'right';
/**
 * Determine taskbard location: "top", "bottom", "left" or "right".
 *
 * Only tested on Windows for now, and only used in Windows.
 *
 * @param tray - The Electron Tray instance.
 */
export declare function taskbarLocation(tray: Tray): TaskbarLocation;
type WindowPosition = 'trayCenter' | 'topRight' | 'trayBottomCenter' | 'bottomLeft' | 'bottomRight';
/**
 * Depending on where the taskbar is, determine where the window should be
 * positioned.
 *
 * @param tray - The Electron Tray instance.
 */
export declare function getWindowPosition(tray: Tray): WindowPosition;
export {};
