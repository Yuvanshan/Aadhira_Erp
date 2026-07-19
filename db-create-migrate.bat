@echo off
setlocal enabledelayedexpansion

echo ========================================
echo AADHIRA ERP - CREATE DB & MIGRATE
echo ========================================
echo.
echo This script will:
  - create the database if it does not exist
  - run Laravel migrations

echo.
set "MYSQL_CMD=mysql"
where mysql >nul 2>&1
if errorlevel 1 (
    if exist "%~dp0server\mariadb\bin\mysql.exe" (
        set "MYSQL_CMD=%~dp0server\mariadb\bin\mysql.exe"
    ) else (
        echo ERROR: MySQL/MariaDB client not found in PATH or in "%~dp0server\mariadb\bin".
        echo Install MySQL/MariaDB or add the client binary to PATH.
        pause
        exit /b 1
    )
)

echo Using database client: %MYSQL_CMD%

echo.
echo Step 1: Create database if not existing...
"%MYSQL_CMD%" -u root -p -e "CREATE DATABASE IF NOT EXISTS aadhira_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo ERROR: database creation failed.
    pause
    exit /b 1
)

echo Database created or already exists.

echo.
echo Step 2: Prepare Laravel environment...
cd /d "%~dp0app\pos_system"
if not exist .env (
    if exist .env.example (
        copy /Y .env.example .env >nul
        echo Copied .env.example to .env
    ) else (
        echo ERROR: .env not found and .env.example is missing.
        pause
        exit /b 1
    )
)

echo.
echo Step 3: Run migrations...
php artisan migrate --force
if errorlevel 1 (
    echo ERROR: migrations failed.
    pause
    exit /b 1
)

echo.
echo ========================================
echo MIGRATIONS COMPLETED SUCCESSFULLY
echo ========================================

echo You can now run the application from the Electron folder.
pause