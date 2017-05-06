@echo off
SETLOCAL

FOR /F "delims=" %%i IN ('npm config get prefix') DO SET BASE_NODE_PATH=%%i
SET NODE_PATH=%BASE_NODE_PATH%\node_modules

IF [%1]==[] GOTO :help

node "%~dp0dts_to_haxe\\DtsToHaxe.js" %*
goto exit

:help
echo Ensure what nodejs, npm and typescript are installed.
node "%~dp0dts_to_haxe\\DtsToHaxe.js"

:exit
ENDLOCAL