import Store from "electron-store";
import { notifyLaravel } from "./utils";
const settingsStore = new Store();
settingsStore.onDidAnyChange((newValue, oldValue) => {
    const changedKey = Object.keys(newValue).find((key) => newValue[key] !== oldValue[key]);
    if (changedKey) {
        notifyLaravel("events", {
            event: "Native\\Laravel\\Events\\Settings\\SettingChanged",
            payload: {
                key: changedKey,
                value: newValue[changedKey] || null,
            },
        });
    }
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
