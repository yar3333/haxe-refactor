@echo off
SETLOCAL

IF [%1]==[] GOTO :help

:file
IF [%2]==[] (SET DEST=%~dpn1.hx) ELSE (SET DEST=%2)
php "%~dp0php_to_haxe.php" extern "%1" "%DEST%"
haxelib run refactor processFile --exclude-string-literals --exclude-comments "%DEST%" %~dp0..\rules\php_to_haxe.rules
haxelib run refactor processFile "%DEST%" %~dp0..\rules\php_to_haxe_extern.rules
goto exit

:help
echo Using: %~n0 ^<srcFile.php^> [ ^<destFile.hx^> ]
echo.

:exit
ENDLOCAL