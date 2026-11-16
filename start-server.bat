@echo off
title EXPIROCHAIN Local Web Server
echo =======================================================
echo          EXPIROCHAIN LOCAL SERVER
echo =======================================================
echo.
echo  Starting server at http://localhost:8000 ...
echo  Keep this window OPEN while using the application.
echo  To STOP the server, press Ctrl+C or close this window.
echo.
echo =======================================================

set PHP_BIN="C:\Users\Asus\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"

if not exist %PHP_BIN% (
    set PHP_BIN=php
)

:: Open browser automatically
start http://localhost:8000

:: Run PHP built-in server listening on all interfaces on port 8000
%PHP_BIN% -S 0.0.0.0:8000 -t "%~dp0."
pause
