@echo off

IF [%1]==[] GOTO :help

haxelib run refactor process -es -ec "%1" *.hx beauty_haxe.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
