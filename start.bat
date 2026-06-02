@echo off
title Akatabo - Starting Services
echo.
echo ============================================
echo   Starting Akatabo Services
echo ============================================
echo.

:: Start XAMPP Apache & MySQL
echo [1/3] Starting XAMPP (Apache + MySQL)...
start /min "" "C:\xampp\xampp_start.exe"

:: Wait for MySQL to be ready
echo [2/3] Waiting for MySQL...
:wait_mysql
"C:\xampp\mysql\bin\mysql.exe" -u root -e "SELECT 1" >nul 2>&1
if errorlevel 1 (
    timeout /t 2 /nobreak >nul
    goto wait_mysql
)
echo   MySQL is ready.

:: Start Laravel dev server
echo [3/3] Starting Laravel dev server...
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
