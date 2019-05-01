@echo on
SETLOCAL

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
IF [%2]==[] (SET DEST=%~dpn1.cs) ELSE (SET DEST=%2)
haxelib run refactor convertFile %3 %4 "%1" "%DEST%" %~dp0..\rules\java_to_haxe.rules
goto exit

:dir
IF [%2]==[] GOTO :help
haxelib run refactor convert %3 %4 "%1" *.java "%2" /[.]java$/.hx/ %~dp0..\rules\java_to_haxe.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^> [--exclude-string-literals] [--exclude-comments]
echo.

:exit
ENDLOCAL
