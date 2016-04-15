@echo off
SETLOCAL

IF [%1]==[] GOTO :help

:file
IF [%2]==[] (SET DEST=%~dpn1.hx) ELSE (SET DEST=%2)
haxelib run refactor convertFile "%1" "%DEST%" %~dp0..\rules\php_to_haxe_typedef.rules
goto exit

:help
echo Using: %~n0 ^<srcFile.php^> [ ^<destFile.hx^> ]
echo.

:exit
ENDLOCAL
