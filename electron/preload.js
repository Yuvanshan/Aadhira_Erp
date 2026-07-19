const { contextBridge, ipcRenderer } = require('electron');

// Expose IPC for splash screen and main app
contextBridge.exposeInMainWorld('ipcRenderer', {
  on: (channel, listener) => {
    ipcRenderer.on(channel, listener);
    // Return unsubscribe function
    return () => ipcRenderer.removeListener(channel, listener);
  },
  once: (channel, listener) => {
    ipcRenderer.once(channel, listener);
  },
  send: (channel, data) => ipcRenderer.send(channel, data),
  invoke: (channel, data) => ipcRenderer.invoke(channel, data),
  removeListener: (channel, listener) => ipcRenderer.removeListener(channel, listener)
});

// Expose safe APIs to the renderer (Laravel front-end)
contextBridge.exposeInMainWorld('electronAPI', {
    // Quit the app from frontend JS
    quitApp: () => ipcRenderer.send('app-quit'),

    // Error handling
    goHome: () => ipcRenderer.send('go-home'),
    retryLoad: () => ipcRenderer.send('retry-load'),

    // You can add more IPC methods here if needed in the future
    // Example: check server status, get app version, etc.
    sendMessage: (channel, data) => {
        ipcRenderer.send(channel, data);
    },
    onMessage: (channel, callback) => {
        ipcRenderer.on(channel, (event, ...args) => callback(...args));
    }
});
