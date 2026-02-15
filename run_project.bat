@echo off
TITLE Cause Society - Project Setup & Run

echo ======================================================
echo       Cause Society - Automated Setup Script
echo ======================================================
echo.
echo IMPORTANT:
echo 1. Ensure XAMPP is running (Apache ^& MySQL).
echo 2. Ensure Node.js and Composer are installed.
echo.
echo Press any key to start initialization...
pause >nul

REM Check for .env file
echo.
if not exist ".env" (
    echo [INFO] .env file not found. Creating from .env.example...
    copy .env.example .env
    echo [OK] .env file created.
) else (
    echo [INFO] .env file already exists. Skipping copy.
)

REM Enable Zip Extension
echo.
echo [STEP 0/6] Ensuring PHP Zip extension is enabled...
powershell -Command "$phpIni = php --ini | Select-String 'Loaded Configuration File:\s+(.*)' -AllMatches | ForEach-Object { $_.Matches.Groups[1].Value.Trim() }; if ($phpIni) { $content = Get-Content $phpIni; $newContent = $content -replace ';extension=zip', 'extension=zip'; Set-Content $phpIni $newContent; echo '[OK] Checked zip extension in '$phpIni }"

REM Ensure Directories Exist
if not exist "bootstrap" mkdir "bootstrap"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"
if not exist "storage" mkdir "storage"
if not exist "storage\framework" mkdir "storage\framework"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\logs" mkdir "storage\logs"

echo [DEBUG] Verifying bootstrap/cache creation...
if exist "bootstrap\cache" (
    echo [OK] bootstrap/cache exists.
    echo [INFO] Clearing old cache files and removing Read-Only attribute...
    attrib -r "bootstrap\cache" /s /d
    del /q "bootstrap\cache\*.php" 2>nul
) else (
    echo [ERROR] Failed to create bootstrap/cache. Check permissions!
    pause
    exit /b 1
)

REM Install PHP Dependencies
echo.
echo [STEP 1/6] Installing PHP dependencies (Composer)...
if exist "composer.lock" del "composer.lock"
if exist "vendor" (
    echo [INFO] Removing existing vendor directory to clear locks...
    rmdir /s /q "vendor"
)
call composer clear-cache
call composer update --no-interaction --no-dev
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer install failed. Please check your Composer installation.
    pause
    exit /b %ERRORLEVEL%
)

REM Install Node Dependencies
echo.
echo [STEP 2/6] Installing Node.js dependencies...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] NPM install failed. Please check your Node.js installation.
    pause
    exit /b %ERRORLEVEL%
)

REM Build Frontend Assets
echo.
echo [STEP 3/6] Building frontend assets (Vite)...
call npm run build
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] NPM build failed.
    pause
    exit /b %ERRORLEVEL%
)

REM Generate App Key
echo.
echo [STEP 4/6] Generating Application Key...
call php artisan key:generate

REM Run Migrations
echo.
echo [STEP 5/6] Running Database Migrations...
echo NOTE: Ensure your database (check .env) exists in phpMyAdmin.
call php artisan migrate --force
if %ERRORLEVEL% NEQ 0 (
    echo [WARNING] Migration failed. Check your database connection in .env.
    echo You may need to create the database manually in phpMyAdmin.
)

REM Link Storage
echo.
echo [STEP 6/6] Linking Storage...
call php artisan storage:link

echo.
echo ======================================================
echo       Setup Complete! Starting Server...
echo ======================================================
echo.
echo The application will open in your default browser.
echo Press Ctrl+C in this window to stop the server.
echo.

REM Open Browser
start http://127.0.0.1:8000

REM Start Server
php artisan serve
