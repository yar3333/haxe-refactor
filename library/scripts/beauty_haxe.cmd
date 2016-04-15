@echo off

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
haxelib run refactor processFile -es -ec "%1" %~dp0..\rules\beauty_haxe.rules
goto exit

:dir
haxelib run refactor process -es -ec "%1" *.hx %~dp0..\rules\beauty_haxe.rules
goto exit

:help
echo Using: %~n0 ^<src^>
echo.

:exit
