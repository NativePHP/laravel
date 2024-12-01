const { contextBridge, ipcRenderer } = require('electron');
const remote = require('@electron/remote');
const Native = {
    on: (event, callback) => {
        ipcRenderer.on('native-event', (_, data) => {
            event = event.replace(/^(\\)+/, '');
            data.event = data.event.replace(/^(\\)+/, '');
            if (event === data.event) {
                return callback(data.payload, event);
            }
        });
    },
    contextMenu: (template) => {
        let menu = remote.Menu.buildFromTemplate(template);
        menu.popup({ window: remote.getCurrentWindow() });
    }
};
window.Native = Native;
window.remote = remote;
ipcRenderer.on('log', (event, { level, message, context }) => {
    if (level === 'error') {
        console.error(`[${level}] ${message}`, context);
    }
    else if (level === 'warn') {
        console.warn(`[${level}] ${message}`, context);
    }
    else {
        console.log(`[${level}] ${message}`, context);
    }
});
ipcRenderer.on('native-event', (event, data) => {
    data.event = data.event.replace(/^(\\)+/, '');
    if (window.Livewire) {
        window.Livewire.dispatch('native:' + data.event, data.payload);
    }
    if (window.livewire) {
        window.livewire.components.components().forEach(component => {
            if (Array.isArray(component.listeners)) {
                component.listeners.forEach(event => {
                    if (event.startsWith('native')) {
                        let event_parts = event.split(/(native:|native-)|:|,/);
                        if (event_parts[1] == 'native:') {
                            event_parts.splice(2, 0, 'private', undefined, 'nativephp', undefined);
                        }
                        let [s1, signature, channel_type, s2, channel, s3, event_name,] = event_parts;
                        if (data.event === event_name) {
                            window.livewire.emit(event, data.payload);
                        }
                    }
                });
            }
        });
    }
});
