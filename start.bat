@echo off
title Akatabo - Starting Services
cd /d "%~dp0"
echo.
echo ============================================
echo   Starting Akatabo Services
echo ============================================
echo.

:: Run migrations if pending
echo [1/5] Running pending migrations...
"C:\xampp\php\php.exe" artisan migrate --force 2>&1 | findstr /v "Nothing to migrate"
echo.

:: Build Vite assets for production
echo [2/5] Building frontend assets...
call npm.cmd run build >nul 2>&1
echo   Done.

:: Cache everything for max speed
echo [3/5] Caching routes, config, views, events...
"C:\xampp\php\php.exe" artisan optimize >nul 2>&1
"C:\xampp\php\php.exe" artisan filament:optimize >nul 2>&1
echo   Done.

:: Start XAMPP Apache & MySQL
echo [4/5] Starting XAMPP (Apache + MySQL)...
start /min "" "C:\xampp\xampp_start.exe"

:: Wait for MySQL to be ready
echo [5/5] Waiting for MySQL...
:wait_mysql
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 1" >nul 2>&1
if errorlevel 1 (
    timeout /t 2 /nobreak >nul
    goto wait_mysql
)
echo   MySQL is ready.

:: Start Laravel dev server
echo.
echo   App:       http://localhost:8000
echo   Dashboard: http://localhost:8000/app
echo   Press Ctrl+C to stop all services
echo.
echo ============================================
"C:\xampp\php\php.exe" artisan serve --host=0.0.0.0 --port=8000

:: When artisan serve stops, stop XAMPP
echo.
echo Stopping XAMPP...
"C:\xampp\xampp_stop.exe"
echo Done.
pause
