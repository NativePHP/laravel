import { session } from "electron";
import state from "./state";
import axios from "axios";

export async function appendCookie() {
  const cookie = {
    url: `http://localhost:${state.phpPort}`,
    name: "_php_native",
    value: state.randomSecret,
  };
  await session.defaultSession.cookies.set(cookie);
}

export async function notifyLaravel(endpoint: string, payload = {}) {
  if (endpoint === 'events') {
    broadcastToWindows('native-event', payload);
  }

  try {
    await axios.post(
      `http://127.0.0.1:${state.phpPort}/_native/api/${endpoint}`,
      payload,
      {
        headers: {
          "X-NativePHP-Secret": state.randomSecret,
        },
      }
    );
  } catch (e) {
    //
  }
}

export function broadcastToWindows(event, payload) {
    Object.values(state.windows).forEach(window => {
        window.webContents.send(event, payload);
    })

    if (state.activeMenuBar?.window) {
        state.activeMenuBar.window.webContents.send(event, payload)
    }
}

/**
 * Remove null and undefined values from an object
 */
export function trimOptions(options: any): any {
  Object.keys(options).forEach(key => options[key] == null && delete options[key]);

  return options;
}
