import { electronAPI } from '@electron-toolkit/preload'
import * as remote from '@electron/remote/index.js'

window.electron = electronAPI
window.remote = remote;
