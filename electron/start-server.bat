@echo off
title Mahdev ERP Engine

echo ===============================
echo   Starting Mahdev ERP Engine
echo ===============================

REM --- Start MariaDB ---
echo Starting Database Server...
start "" /B "C:\Aadhira_erp_v_1.0\server\mariadb\bin\mysqld.exe" --console --port=3307

echo Waiting for Database...
timeout /t 6 >nul

REM --- Start PHP Server ---
echo Starting PHP Server...
cd /d C:\Aadhira_erp_v_1.0\app\pos_system
start "" /B "C:\Aadhira_erp_v_1.0\server\php\php.exe" -S 127.0.0.1:8888 -t public

echo Waiting for PHP server...
timeout /t 4 >nul

REM --- Build config cache only if missing (avoids extra startup delay)
if not exist "C:\Aadhira_erp_v_1.0\app\pos_system\bootstrap\cache\config.php" (
	echo Building Laravel config cache...
	"C:\Aadhira_erp_v_1.0\server\php\php.exe" artisan config:cache
)

REM --- Start Electron App ---
echo Starting Mahdev ERP Desktop...
cd /d C:\Aadhira_erp_v_1.0\electron
npm start
