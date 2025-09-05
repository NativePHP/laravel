/**
 * Entry point of menubar
 * @example
 * ```typescript
 * import { menubar } from 'menubar';
 * ```
 */

/** */

import { app } from 'electron';

import { Menubar } from './Menubar.js';
import type { Options } from './types.js';

export * from './util/getWindowPosition.js';
export { Menubar };

/**
 * Factory function to create a menubar application
 *
 * @param options - Options for creating a menubar application, see
 * {@link Options}
 */
export function menubar(options?: Partial<Options>): Menubar {
  return new Menubar(app, options);
}
