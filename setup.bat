@echo off
echo ========================================
echo AADHIRA ERP - FIRST TIME SETUP
echo ========================================
echo.
echo This script will set up the database and run initial migrations.
echo Make sure you have PHP, Composer, and MySQL installed.
echo.
pause

echo.
echo Step 1: Creating database...
echo Please enter MySQL root password when prompted.
set "MYSQL_CMD=mysql"
where mysql >nul 2>&1
if %errorlevel% neq 0 (
    if exist "%~dp0server\mariadb\bin\mysql.exe" (
        set "MYSQL_CMD=%~dp0server\mariadb\bin\mysql.exe"
    ) else (
        echo MySQL/MariaDB client not found in PATH, and bundled MariaDB was not found.
        echo Install MySQL or MariaDB, or place the server binaries in %~dp0server\mariadb\bin.
        pause
        exit /b 1
    )
)

"%MYSQL_CMD%" -u root -p -e "CREATE DATABASE IF NOT EXISTS aadhira_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if %errorlevel% neq 0 (
    echo Error creating database. Please check MySQL installation and try again.
    pause
    exit /b 1
)

echo Database created successfully.

echo.
echo Step 2: Installing PHP dependencies...
cd app\pos_system
composer install

if %errorlevel% neq 0 (
    echo Error installing PHP dependencies. Please check Composer installation.
    pause
    exit /b 1
)

echo PHP dependencies installed.

echo.
echo Step 3: Copying environment file...
if not exist .env (
    copy .env.example .env
    echo .env file copied. Please edit .env file with your database credentials.
    notepad .env
)

echo.
echo Step 4: Generating application key...
php artisan key:generate

echo.
echo Step 5: Running database migrations...
php artisan migrate

if %errorlevel% neq 0 (
    echo Error running migrations. Please check database connection.
    pause
    exit /b 1
)

echo Migrations completed.

echo.
echo Step 6: Seeding database...
php artisan db:seed

if %errorlevel% neq 0 (
    echo Error seeding database. Please check seeder files.
    pause
    exit /b 1
)

echo Database seeded.

echo.
echo Step 7: Creating storage link...
php artisan storage:link

echo.
echo Step 8: Clearing and caching config...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo Step 9: Setting up Node.js dependencies for Electron...
cd ..\..\electron
npm install

if %errorlevel% neq 0 (
    echo Error installing Node.js dependencies.
    pause
    exit /b 1
)

echo.
echo ========================================
echo SETUP COMPLETED SUCCESSFULLY!
echo ========================================
echo.
echo You can now run the application using:
echo cd electron
echo npm start
echo.
echo Or build the desktop app with:
echo npm run build
echo.
pause