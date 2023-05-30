import { contextBridge, ipcRenderer } from 'electron'
import { electronAPI } from '@electron-toolkit/preload'
import * as remote from '@electron/remote'

// Custom APIs for renderer
const api = {}

window.electron = electronAPI
window.remote = remote;
window.api = api
