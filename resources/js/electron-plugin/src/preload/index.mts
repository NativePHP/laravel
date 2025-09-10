import remote from "@electron/remote";
import {ipcRenderer, contextBridge} from "electron";

// -------------------------------------------------------------------
// The Native helper
// -------------------------------------------------------------------
const Native = {
    on: (event, callback) => {
        ipcRenderer.on('native-event', (_, data) => {
            // Strip leading slashes
            event = event.replace(/^(\\)+/, '');
            data.event = data.event.replace(/^(\\)+/, '');

            if (event === data.event) {
                return callback(data.payload, event);
            }
        })
    },
    contextMenu: (template) => {
        let menu = remote.Menu.buildFromTemplate(template);
        menu.popup({ window: remote.getCurrentWindow() });
    }
};

contextBridge.exposeInMainWorld('Native', Native);

// -------------------------------------------------------------------
// Log events
// -------------------------------------------------------------------
ipcRenderer.on('log', (event, {level, message, context}) => {
    if (level === 'error') {
      console.error(`[${level}] ${message}`, context)
    } else if (level === 'warn') {
      console.warn(`[${level}] ${message}`, context)
    } else {
      console.log(`[${level}] ${message}`, context)
    }
});


// -------------------------------------------------------------------
// Livewire event listeners
// -------------------------------------------------------------------
ipcRenderer.on('native-event', (event, data) => {

  // Strip leading slashes
  data.event = data.event.replace(/^(\\)+/, '');

  // add support for livewire 3
  // @ts-ignore
  if (window.Livewire) {
    // @ts-ignore
    window.Livewire.dispatch('native:' + data.event, data.payload);
  }

  // add support for livewire 2
  // @ts-ignore
  if (window.livewire) {
    // @ts-ignore
    window.livewire.components.components().forEach(component => {
      if (Array.isArray(component.listeners)) {
        component.listeners.forEach(event => {
          if (event.startsWith('native')) {
            let event_parts = event.split(/(native:|native-)|:|,/)

            if (event_parts[1] == 'native:') {
              event_parts.splice(2, 0, 'private', undefined, 'nativephp', undefined)
            }

            let [
              s1,
              signature,
              channel_type,
              s2,
              channel,
              s3,
              event_name,
            ] = event_parts

            if (data.event === event_name) {
              // @ts-ignore
              window.livewire.emit(event, data.payload)
            }
          }
        })
      }
    })
  }
})

// -------------------------------------------------------------------
// Let the client know preload is fully evaluated
// -------------------------------------------------------------------
contextBridge.exposeInMainWorld('native:initialized', (function() {
    // This is admittedly a bit hacky. Due to context isolation
    // we don't have direct access to the renderer window object,
    // but by assigning a bridge function that executes itself inside
    // the renderer context we can hack around it.

    // It's recommended to use window.postMessage & dispatch an
    // event from the renderer itself, but we're loading webcontent
    // from localhost. We don't have a renderer process we can access.
    // Though this is hacky it works well and is the quickest way to do this
    // without sprinkling additional logic all over the place.

    window.dispatchEvent(new CustomEvent('native:init'));

    return true;
})())
