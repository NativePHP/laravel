import { contextBridge, ipcRenderer } from 'electron'
import { electronAPI } from '@electron-toolkit/preload'
import * as remote from '@electron/remote'

window.electron = electronAPI
window.remote = remote;
