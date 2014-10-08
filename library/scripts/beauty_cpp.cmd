@echo off

IF [%1]==[] GOTO :help

haxelib run refactor process -es -ec "%1" *.c;*.cpp;*.h beauty_cpp.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
