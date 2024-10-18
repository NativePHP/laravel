import { ipcRenderer } from 'electron'

export default {
    on: (event, callback) => {
        ipcRenderer.on('native-event', (_, data) => {

            // Strip leading slashes
            event = event.replace(/^(\\)+/, '');
            data.event = data.event.replace(/^(\\)+/, '');

            if(event === data.event) {
                return callback(data.payload, event);
            }
        })
    }
}
