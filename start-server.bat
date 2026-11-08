@echo off
title EXPIROCHAIN Local Server
echo =======================================================
echo          EXPIROCHAIN LOCAL SERVER
echo =======================================================
echo  Website URL: http://localhost:8000
echo  Press Ctrl+C to stop the server.
echo =======================================================
"C:\Users\Asus\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.2_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" -S localhost:8000 -t "%~dp0."
pause
