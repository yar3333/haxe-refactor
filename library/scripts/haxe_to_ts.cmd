@echo off
SETLOCAL

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
IF [%2]==[] (SET DEST=%~dpn1.ts) ELSE (SET DEST=%2)
haxelib run refactor convertFile --exclude-string-literals --exclude-comments "%1" "%DEST%" %~dp0..\rules\haxe_to_ts.rules
goto exit

:dir
IF [%2]==[] GOTO :help
haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.hx "%2" /[.]hx$/.ts/ %~dp0..\rules\haxe_to_ts.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
ENDLOCAL
