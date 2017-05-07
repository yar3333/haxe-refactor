@echo off
SETLOCAL

FOR /F "delims=" %%i IN ('npm config get prefix') DO SET BASE_NODE_PATH=%%i
SET NODE_PATH=%BASE_NODE_PATH%\node_modules

node %*

ENDLOCAL