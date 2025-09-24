import { fileURLToPath } from 'url'

let preloadPath = fileURLToPath(new URL('../../electron-plugin/dist/preload/index.mjs', import.meta.url));

const defaultWebPreferences = {
    spellcheck: false,
    nodeIntegration: false,
    backgroundThrottling: false,
};

const requiredWebPreferences = {
    sandbox: false,
    preload: preloadPath,
    contextIsolation: true,
}

export default function(userWebPreferences: object = {}): object
{
    return {
        ...defaultWebPreferences,
        ...userWebPreferences,
        ...requiredWebPreferences
    }
}
