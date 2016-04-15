@echo off
SETLOCAL

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
IF [%2]==[] (SET DEST=%~dpn1.hx) ELSE (SET DEST=%2)
haxelib run refactor convertFile "%1" "%DEST%" %~dp0..\rules\cs_to_haxe_comments.rules
haxelib run refactor processFile --exclude-string-literals --exclude-comments "%DEST%" %~dp0..\rules\cs_to_haxe.rules
goto exit

:dir
IF [%2]==[] GOTO :help
haxelib run refactor convert "%1" *.cs "%2" /[.]cs$/.hx/ %~dp0..\rules\cs_to_haxe_comments.rules
haxelib run refactor process --exclude-string-literals --exclude-comments "%2" *.hx %~dp0..\rules\cs_to_haxe.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
ENDLOCAL
