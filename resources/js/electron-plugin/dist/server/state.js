import Store from "electron-store";
import { notifyLaravel } from "./utils.js";
const settingsStore = new Store();
settingsStore.onDidAnyChange((newValue, oldValue) => {
    const changedKeys = Object.keys(newValue).filter((key) => newValue[key] !== oldValue[key]);
    changedKeys.forEach((key) => {
        notifyLaravel("events", {
            event: "Native\\Laravel\\Events\\Settings\\SettingChanged",
            payload: {
                key,
                value: newValue[key] || null,
            },
        });
    });
});
function generateRandomString(length) {
    let result = "";
    const characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    const charactersLength = characters.length;
    for (let i = 0; i < length; i += 1) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}
export default {
    electronApiPort: null,
    activeMenuBar: null,
    tray: null,
    php: null,
    phpPort: null,
    phpIni: null,
    caCert: null,
    icon: null,
    store: settingsStore,
    randomSecret: generateRandomString(32),
    processes: {},
    windows: {},
    findWindow(id) {
        return this.windows[id] || null;
    },
};
