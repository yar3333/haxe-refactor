@echo off

IF [%1]==[] GOTO :help

haxelib run refactor convert %1 [.]html$ %1 /// html_beauty.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
