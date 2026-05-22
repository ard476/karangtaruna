@echo off
cd /d "%~dp0"
title Karang Taruna - Server HP
echo.
php artisan app:lan --port=8001
echo.
echo Menjalankan server (Ctrl+C untuk berhenti)...
echo.
php artisan serve --host=0.0.0.0 --port=8001
