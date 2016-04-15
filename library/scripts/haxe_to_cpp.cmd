@echo off
SETLOCAL

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
IF [%2]==[] (SET DEST=%~dpn1.cpp) ELSE (SET DEST=%2)
haxelib run refactor convertFile --exclude-string-literals --exclude-comments "%1" "%DEST%" %~dp0..\rules\haxe_to_cpp.rules
goto exit

:dir
IF [%2]==[] GOTO :help
haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.hx "%2" /[.]hx$/.cpp/ %~dp0..\rules\haxe_to_cpp.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
ENDLOCAL
