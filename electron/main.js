// -----------------------------
// Imports
// -----------------------------
const { app, BrowserWindow, ipcMain, globalShortcut } = require("electron");
const path = require("path");
const fs = require("fs");
const { spawn } = require("child_process");
const http = require("http");
const net = require("net");

let mainWindow;
let splashWindow;
let phpProcess;
let dbProcess;
let startupCompleted = false;
let isErrorPage = false;

// -----------------------------
// Load Application Configuration
// -----------------------------
const CONFIG_FILE = path.join(__dirname, "config.json");
let appConfig = {
  mode: "local",
  url: "http://127.0.0.1:8888"
};

try {
  if (fs.existsSync(CONFIG_FILE)) {
    const configData = fs.readFileSync(CONFIG_FILE, "utf8");
    appConfig = JSON.parse(configData);
    console.log("Loaded configuration:", appConfig);
  } else {
    fs.writeFileSync(CONFIG_FILE, JSON.stringify(appConfig, null, 2), "utf8");
    console.log("Created default configuration file");
  }
} catch (error) {
  console.error("Error reading config.json, defaulting to local mode:", error.message);
}

// -----------------------------
// Detect real base path
// In packaged app, resources are in resources/ folder, NOT in process.resourcesPath
// process.resourcesPath points to app.asar location
// extraResources are unpacked to resources/ folder next to app root
// -----------------------------
const isDev = !app.isPackaged;

let BASE_PATH;
if (isDev) {
  BASE_PATH = path.join(__dirname, "..");
} else {
  // In packaged app, extraResources are in resources/ folder at app root level
  BASE_PATH = path.join(path.dirname(process.execPath), "resources");
}

const PHP_PATH    = path.join(BASE_PATH, "server", "php", "php.exe");
const MYSQLD_PATH = path.join(BASE_PATH, "server", "mariadb", "bin", "mysqld.exe");
const MYSQL_CONFIG_PATH = path.join(BASE_PATH, "server", "mariadb", "data", "my.ini");
const MARIADB_BASE_PATH = path.join(BASE_PATH, "server", "mariadb");
const LARAVEL_PATH = path.join(BASE_PATH, "app", "pos_system");

function getMariaDbPort() {
  try {
    const config = fs.readFileSync(MYSQL_CONFIG_PATH, "utf8");
    const match = config.match(/^port\s*=\s*(\d+)$/m);

    if (match) {
      return Number.parseInt(match[1], 10);
    }
  } catch (error) {
    console.warn("Unable to read MariaDB port from my.ini, using default:", error.message);
  }

  return 3307;
}

const MARIADB_PORT = getMariaDbPort();

// Debugging: log paths on startup
if (!isDev) {
  console.log("Packaged app paths:");
  console.log("BASE_PATH:", BASE_PATH);
  console.log("PHP_PATH:", PHP_PATH);
  console.log("MYSQLD_PATH:", MYSQLD_PATH);
}

// -----------------------------
// Helper: send to splash safely
// Only fires if splashWindow exists and its webContents is not destroyed
// Supports both status messages and error messages
// -----------------------------
function splashSend(message, type = "status") {
  console.log(`[${type.toUpperCase()}] ${message}`);
  
  if (
    splashWindow &&
    !splashWindow.isDestroyed() &&
    splashWindow.webContents &&
    !splashWindow.webContents.isDestroyed()
  ) {
    splashWindow.webContents.send("splash-progress", { message, type });
  }
}

function isPortOpen(port, host = "127.0.0.1") {
  return new Promise((resolve) => {
    const socket = new net.Socket();
    let settled = false;

    const finish = (result) => {
      if (!settled) {
        settled = true;
        socket.destroy();
        resolve(result);
      }
    };

    socket.setTimeout(500);
    socket.once("connect", () => finish(true));
    socket.once("timeout", () => finish(false));
    socket.once("error", () => finish(false));
    socket.connect(port, host);
  });
}

// -----------------------------
// Splash Screen
// Improved with better error handling
// -----------------------------
function createSplash() {
  console.log("Creating splash window...");
  
  try {
    splashWindow = new BrowserWindow({
      width: 500,
      height: 350,
      frame: false,
      transparent: true,
      resizable: false,
      alwaysOnTop: true,
      icon: path.join(__dirname, "assets", "app.ico"),
      webPreferences: {
        // Splash needs contextIsolation + preload to receive IPC
        preload: path.join(__dirname, "preload.js"),
        contextIsolation: true,
        enableRemoteModule: false,
        sandbox: true
      }
    });

    console.log("Loading splash.html...");
    splashWindow.loadFile(path.join(__dirname, "splash.html"));

    // Handle splash load errors
    splashWindow.webContents.on("did-fail-load", (event, errorCode, errorDescription) => {
      console.error(`Failed to load splash: ${errorCode} - ${errorDescription}`);
    });

    // Only send IPC after the page has fully loaded
    splashWindow.webContents.once("did-finish-load", () => {
      console.log("Splash page loaded, sending initial message...");
      splashSend("Starting services...");
    });

    // If splash is closed externally, don't try to send messages
    splashWindow.on("closed", () => {
      console.log("Splash window closed");
      splashWindow = null;
    });

  } catch (error) {
    console.error("Error creating splash window:", error);
  }
}

// -----------------------------
// Start MariaDB and resolve when the port is open
// Polls the configured MariaDB port — avoids the variable-time startup race condition
// Captures stderr for error reporting
// -----------------------------
function startDatabase() {
  return new Promise(async (resolve) => {
    splashSend("Starting database...");

    if (await isPortOpen(MARIADB_PORT)) {
      console.log(`MariaDB port ${MARIADB_PORT} already in use; reusing existing database instance`);
      splashSend("Using existing database service...");
      resolve(true);
      return;
    }
    
    dbProcess = spawn(MYSQLD_PATH, [
      `--defaults-file=${MYSQL_CONFIG_PATH}`,
      "--console",
      `--port=${MARIADB_PORT}`
    ], {
      cwd: MARIADB_BASE_PATH,
      stdio: ["ignore", "pipe", "pipe"], // Capture stdout and stderr for logging
      detached: false,
      windowsHide: true // Hide console window on Windows
    });

    dbProcess.on("error", (err) => {
      console.error("MariaDB spawn error:", err.message);
      splashSend(`Database error: ${err.message}`, "error");
      resolve(false);
    });

    dbProcess.once("exit", (code, signal) => {
      dbProcess = null;
      console.warn(`MariaDB process exited early with code ${code}, signal ${signal}`);
    });

    // Capture stderr for debugging
    if (dbProcess.stderr) {
      dbProcess.stderr.on("data", (data) => {
        const message = data.toString().trim();
        if (message && !message.includes("ready for connections")) {
          console.log("[MariaDB]", message);
        }
      });
    }

    waitForPort(MARIADB_PORT, 30, (isReady) => resolve(isReady));
  });
}

// -----------------------------
// Poll a TCP port until it accepts connections
// retries: max attempts, onReady: callback when open (or exhausted)
// Improved with better error handling
// -----------------------------
function waitForPort(port, retries, onReady) {
  let attemptsRemaining = retries;
  let settled = false;

  const finish = (result) => {
    if (!settled) {
      settled = true;
      onReady(result);
    }
  };
  
  const attempt = () => {
    const sock = new net.Socket();
    sock.setTimeout(500);

    sock.connect(port, "127.0.0.1", () => {
      sock.destroy();
      console.log(`Port ${port} is open - service ready`);
      finish(true);
    });

    sock.on("error", () => {
      sock.destroy();
      if (attemptsRemaining-- > 0) {
        setTimeout(attempt, 1000);
      } else {
        // Timed out — proceed and let the app surface the error
        console.warn(`Timeout waiting for port ${port} after ${retries} attempts`);
        splashSend(`Warning: Port ${port} not responding - app may fail`, "warning");
        finish(false);
      }
    });

    sock.on("timeout", () => {
      sock.destroy();
      if (attemptsRemaining-- > 0) {
        setTimeout(attempt, 1000);
      } else {
        console.warn(`Timeout waiting for port ${port}`);
        splashSend(`Warning: Port ${port} timeout`, "warning");
        finish(false);
      }
    });
  };

  attempt();
}

// -----------------------------
// Start PHP Server (called only after DB is ready)
// Captures stderr for debugging
// -----------------------------
function startPhp() {
  splashSend("Starting PHP server...");
  
  phpProcess = spawn(
    PHP_PATH,
    ["-S", "0.0.0.0:8888", "-t", "public", path.join(LARAVEL_PATH, "public", "router.php")],
    {
      cwd: LARAVEL_PATH,
      stdio: ["ignore", "pipe", "pipe"], // Capture stdout and stderr
      detached: false,
      windowsHide: true // Hide console window on Windows
    }
  );

  phpProcess.on("error", (err) => {
    console.error("PHP spawn error:", err.message);
    splashSend(`PHP error: ${err.message}`, "error");
  });

  // Capture stderr for debugging
  if (phpProcess.stderr) {
    phpProcess.stderr.on("data", (data) => {
      const message = data.toString().trim();
      if (message) {
        console.log("[PHP]", message);
      }
    });
  }
  
  // Capture stdout
  if (phpProcess.stdout) {
    phpProcess.stdout.on("data", (data) => {
      const message = data.toString().trim();
      if (message && message.includes("Listening")) {
        console.log("[PHP]", message);
        splashSend("PHP server ready!");
      }
    });
  }
}

// -----------------------------
// Main Window
// Improved with better error handling
// -----------------------------
function createWindow() {
  if (mainWindow && !mainWindow.isDestroyed()) {
    console.log("Main window already exists, focusing existing window");
    if (!mainWindow.isVisible()) {
      mainWindow.show();
    }
    mainWindow.focus();
    return;
  }

  console.log("Creating main window...");
  
  mainWindow = new BrowserWindow({
    width: 1300,
    height: 850,
    minWidth: 1000,
    minHeight: 700,
    show: false,
    title: "Mahdev ERP",
    icon: path.join(__dirname, "assets", "app.ico"),
    webPreferences: {
      preload: path.join(__dirname, "preload.js"),
      contextIsolation: true,
      enableRemoteModule: false,
      sandbox: true
    }
  });

  console.log("Loading URL:", `${appConfig.url}/login`);
  isErrorPage = false;
  mainWindow.loadURL(`${appConfig.url}/login`);

  // Show window once ready
  mainWindow.once("ready-to-show", () => {
    console.log("Main window ready to show");
    try {
      if (splashWindow && !splashWindow.isDestroyed()) {
        splashWindow.close();
      }
      mainWindow.show();
      console.log("Main window shown successfully");
    } catch (err) {
      console.error("Error showing main window:", err);
    }
  });

  // Handle window closed
  mainWindow.on("closed", () => {
    console.log("Main window closed");
    mainWindow = null;
  });

  // Log any errors during page load
  mainWindow.webContents.on("render-process-gone", (event, details) => {
    console.error("Renderer process gone:", details.reason);
  });

  mainWindow.webContents.on("did-fail-load", (event, errorCode, errorDescription) => {
    console.error(`Failed to load page: ${errorCode} - ${errorDescription}`);
    showErrorPage('network', { code: errorCode, description: errorDescription });
  });

  mainWindow.webContents.on("did-finish-load", () => {
    console.log("Page content loaded");
    // Check if this is an error page by examining the content
    checkForServerError();
  });
}

// -----------------------------
// Configuration Setup Window
// -----------------------------
function createWindowWithConfigSetup() {
  if (mainWindow && !mainWindow.isDestroyed()) {
    mainWindow.loadFile(path.join(__dirname, "config_setup.html"));
    return;
  }
  
  mainWindow = new BrowserWindow({
    width: 600,
    height: 500,
    resizable: false,
    title: "Aadhira ERP - Configuration Setup",
    icon: path.join(__dirname, "assets", "app.ico"),
    webPreferences: {
      preload: path.join(__dirname, "preload.js"),
      contextIsolation: true,
      enableRemoteModule: false,
      sandbox: true
    }
  });

  mainWindow.loadFile(path.join(__dirname, "config_setup.html"));
  
  mainWindow.once("ready-to-show", () => {
    if (splashWindow && !splashWindow.isDestroyed()) {
      splashWindow.close();
    }
    mainWindow.show();
  });

  mainWindow.on("closed", () => {
    mainWindow = null;
  });
}

// Function to show error page
function showErrorPage(type, details) {
  if (mainWindow && !mainWindow.isDestroyed() && !isErrorPage) {
    console.log(`Loading error page for ${type} error`);
    isErrorPage = true;
    mainWindow.loadFile(path.join(__dirname, "error.html")).then(() => {
      // Set window title to hide file path
      mainWindow.setTitle('Mahdev ERP - Service Maintenance');
      // Send error details to the error page
      mainWindow.webContents.send('error-details', { type, ...details });
    }).catch(err => {
      console.error('Failed to load error page:', err);
    });
  }
}

// Function to check if current page is a server error page
function checkForServerError() {
  if (!mainWindow || mainWindow.isDestroyed() || isErrorPage) return;

  mainWindow.webContents.executeJavaScript(`
    (function() {
      const title = document.title.toLowerCase();
      const bodyText = document.body ? document.body.textContent.toLowerCase() : '';

      // Check for common error indicators
      const errorIndicators = [
        title.includes('404'),
        title.includes('500'),
        title.includes('503'),
        title.includes('error'),
        bodyText.includes('page not found'),
        bodyText.includes('internal server error'),
        bodyText.includes('service unavailable'),
        document.querySelector('h1') && document.querySelector('h1').textContent.toLowerCase().includes('error')
      ];

      return errorIndicators.some(indicator => indicator);
    })()
  `).then(isErrorPage => {
    if (isErrorPage) {
      console.log("Detected server error page, showing error overlay");
      showErrorPage('server', { code: 'detected', description: 'Server error page detected' });
    }
  }).catch(err => {
    console.log("Could not check for error page:", err);
  });
}

// Handle app-quit from frontend JavaScript
ipcMain.on("app-quit", () => {
  console.log("App quit requested from frontend");
  app.quit();
});

// Handle go-home from error page
ipcMain.on("go-home", () => {
  console.log("Go home requested from error page");
  if (mainWindow && !mainWindow.isDestroyed()) {
    isErrorPage = false;
    mainWindow.loadURL(`${appConfig.url}/home`);
  }
});

// Handle retry-load from error page
ipcMain.on("retry-load", () => {
  console.log("Retry load requested from error page");
  if (mainWindow && !mainWindow.isDestroyed()) {
    isErrorPage = false;
    mainWindow.reload();
  }
});

// Get current application config settings
ipcMain.handle("get-config", () => {
  return appConfig;
});

// Save settings to config.json and reload
ipcMain.on("save-config", async (event, config) => {
  try {
    // Run connection check if cloud mode
    if (config.mode === "cloud") {
      let host = "127.0.0.1";
      let port = 80;
      try {
        const parsedUrl = new URL(config.url);
        host = parsedUrl.hostname;
        port = parsedUrl.port ? parseInt(parsedUrl.port, 10) : (parsedUrl.protocol === "https:" ? 443 : 80);
      } catch (e) {
        event.reply("save-config-status", { success: false, message: "Invalid URL format" });
        return;
      }
      
      const isOnline = await isPortOpen(port, host);
      if (!isOnline) {
        event.reply("save-config-status", { success: false, message: `Cloud host ${host}:${port} is unreachable.` });
        return;
      }
    }

    appConfig = config;
    fs.writeFileSync(CONFIG_FILE, JSON.stringify(appConfig, null, 2), "utf8");
    console.log("Configuration saved successfully:", appConfig);
    
    event.reply("save-config-status", { success: true });
    
    // Reboot the application state or redirect to correct page after a short delay
    setTimeout(() => {
      if (mainWindow && !mainWindow.isDestroyed()) {
        isErrorPage = false;
        
        if (appConfig.mode === "cloud") {
          // Re-create the standard sized main window for actual app
          mainWindow.destroy();
          mainWindow = null;
          createWindow();
        } else {
          // If local, we need to boot local services
          app.relaunch();
          app.quit();
        }
      }
    }, 1000);
  } catch (err) {
    console.error("Failed to save config:", err.message);
    event.reply("save-config-status", { success: false, message: err.message });
  }
});

// Handle settings configuration request
ipcMain.on("open-settings", () => {
  console.log("Opening configuration settings requested");
  createWindowWithConfigSetup();
});

// -----------------------------
// Poll HTTP backend until it returns 200
// Improved with better error handling and feedback
// -----------------------------
function waitForBackend(onReady, retries = 60) {
  splashSend("Waiting for backend...");
  let attemptsRemaining = retries;
  let finished = false;

  const finish = () => {
    if (finished) {
      return;
    }

    finished = true;
    onReady();
  };

  const attempt = () => {
    if (finished) {
      return;
    }

    const req = http.get("http://127.0.0.1:8888/login", (res) => {
      if (res.statusCode === 200 || res.statusCode === 302) {
        // Consume the body so the socket closes cleanly
        res.resume();
        splashSend("Backend ready! Loading app...");
        setTimeout(finish, 150);
      } else {
        res.resume();
        retry();
      }
    });

    req.on("error", (err) => {
      console.error(`HTTP request failed (attempt ${retries - attemptsRemaining + 1}/${retries}):`, err.message);
      retry();
    });
    
    req.setTimeout(2000, () => {
      req.destroy();
      retry();
    });
  };

  const retry = () => {
    if (finished) {
      return;
    }

    if (attemptsRemaining-- > 0) {
      setTimeout(attempt, 500);
    } else {
      // Give up waiting and open the window anyway
      console.warn("Timeout waiting for HTTP backend - opening app anyway");
      splashSend("Opening app (backend may not be ready)...", "warning");
      setTimeout(finish, 150);
    }
  };

  attempt();
}

// Fetch client mode configuration from central server
function fetchClientMode(serverUrl) {
  return new Promise((resolve) => {
    try {
      const parsedUrl = new URL(serverUrl);
      const protocol = parsedUrl.protocol === "https:" ? require("https") : require("http");
      const apiUrl = `${serverUrl.endsWith('/') ? serverUrl.slice(0, -1) : serverUrl}/connector/api/client-mode`;
      
      const req = protocol.get(apiUrl, { timeout: 4000 }, (res) => {
        let data = "";
        res.on("data", (chunk) => { data += chunk; });
        res.on("end", () => {
          try {
            if (res.statusCode === 200) {
              const parsed = JSON.parse(data);
              resolve(parsed.desktop_client_mode || null);
            } else {
              resolve(null);
            }
          } catch (e) {
            resolve(null);
          }
        });
      });
      
      req.on("error", () => {
        resolve(null);
      });
    } catch (e) {
      resolve(null);
    }
  });
}

// -----------------------------
// App Startup Flow
// Comprehensive error handling with detailed logging
// -----------------------------
app.whenReady().then(async () => {
  console.log("======== APP STARTUP BEGIN ========");
  
  // Register admin shortcut to open settings from anywhere
  globalShortcut.register("CommandOrControl+Shift+S", () => {
    console.log("Admin shortcut Ctrl+Shift+S triggered - opening configuration panel");
    createWindowWithConfigSetup();
  });

  // Register developer shortcut to toggle DevTools
  globalShortcut.register("CommandOrControl+Shift+I", () => {
    if (mainWindow && !mainWindow.isDestroyed()) {
      mainWindow.webContents.toggleDevTools();
    }
  });
  console.log("App packaged:", app.isPackaged);
  console.log("App path:", app.getAppPath());
  console.log("BASE_PATH:", BASE_PATH);
  console.log("PHP_PATH:", PHP_PATH);
  console.log("MYSQLD_PATH:", MYSQLD_PATH);
  console.log("LARAVEL_PATH:", LARAVEL_PATH);
  
  createSplash();

  // Give splash a tick to mount before spawning heavy processes
  await new Promise((r) => setTimeout(r, 300));

  // -----------------------------
  // Cloud Mode Integration Bypass
  // -----------------------------
  if (appConfig.mode === "cloud") {
    console.log("Starting in CLOUD mode, connecting to:", appConfig.url);
    splashSend("Connecting to cloud server...");
    
    let host = "127.0.0.1";
    let port = 80;
    try {
      const parsedUrl = new URL(appConfig.url);
      host = parsedUrl.hostname;
      port = parsedUrl.port ? parseInt(parsedUrl.port, 10) : (parsedUrl.protocol === "https:" ? 443 : 80);
    } catch (e) {
      console.error("Invalid URL in config:", appConfig.url);
      splashSend("Invalid cloud URL in config.json", "error");
      return;
    }
    
    const isOnline = await isPortOpen(port, host);
    if (isOnline) {
      splashSend("Checking central configuration...");
      const serverMode = await fetchClientMode(appConfig.url);
      console.log("Central server mode returned:", serverMode);
      
      if (serverMode === "local") {
        console.log("Central server forced local/offline mode. Switching runtime...");
        splashSend("Forced offline mode. Initializing local database...");
        appConfig.mode = "local";
        fs.writeFileSync(CONFIG_FILE, JSON.stringify(appConfig, null, 2), "utf8");
        // Proceed to start local services by falling through (removing return statement)
      } else {
        splashSend("Launching cloud dashboard...");
        setTimeout(() => {
          createWindow();
          console.log("======== APP STARTUP COMPLETE (CLOUD) ========");
        }, 500);
        return;
      }
    } else {
      console.error("Could not reach cloud server:", host, port);
      splashSend("Cloud server unreachable. Opening configuration settings...", "warning");
      setTimeout(() => {
        createWindowWithConfigSetup();
      }, 1500);
      return;
    }
  }

  // Safety timeout: if app takes too long to load, show splash with warning (Local Mode)
  const startupTimeout = setTimeout(() => {
    splashSend("Startup taking longer than expected...\nCheck logs for details", "warning");
    console.warn("App startup timeout warning after 120 seconds");
  }, 120000); // 120 second warning

  try {
    console.log("Step 1: Starting database...");
    // 1. Start DB and wait for it to be ready before starting PHP
    const databaseReady = await startDatabase();
    console.log("Step 1: Database status:", databaseReady ? "ready" : "not ready");

    console.log("Step 2: Starting PHP server...");
    // 2. Start PHP only after DB port is open
    startPhp();

    // 3. Poll until Laravel responds, then open the window.
    console.log("Step 3: Polling backend endpoint...");
    waitForBackend(() => {
      if (startupCompleted) {
        return;
      }

      startupCompleted = true;
      clearTimeout(startupTimeout);
      console.log("Step 4: Creating main window...");
      createWindow();
      console.log("======== APP STARTUP COMPLETE (LOCAL) ========");
    });
  } catch (error) {
    clearTimeout(startupTimeout);
    console.error("======== FATAL STARTUP ERROR ========");
    console.error("Error:", error);
    console.error("Stack:", error.stack);
    splashSend(`Fatal error: ${error.message}`, "error");
  }
});

// -----------------------------
// Force-kill child processes on Windows
// process.kill with SIGTERM is unreliable for .exe on Win32
// Improved with better error handling
// -----------------------------
function killProcess(proc) {
  if (!proc) return;
  try {
    console.log(`Killing process ${proc.pid}...`);
    if (process.platform === "win32" && proc.pid) {
      // taskkill /F kills the process tree, including mysqld forks
      const { execSync } = require("child_process");
      execSync(`taskkill /PID ${proc.pid} /T /F`, { stdio: "ignore" });
      console.log(`Process ${proc.pid} killed`);
    } else {
      proc.kill("SIGKILL");
      console.log(`Process ${proc.pid} killed with SIGKILL`);
    }
  } catch (err) {
    // Process may have already exited
    console.log(`Process cleanup failed (may have already exited):`, err.message);
  }
}

app.on("window-all-closed", () => {
  console.log("All windows closed, cleaning up...");
  killProcess(phpProcess);
  killProcess(dbProcess);
  console.log("Cleanup complete, quitting app");
  app.quit();
});

// Guard against the app being force-quit (e.g. Ctrl+C in dev)
process.on("exit", () => {
  console.log("Process exit event - final cleanup");
  killProcess(phpProcess);
  killProcess(dbProcess);
});