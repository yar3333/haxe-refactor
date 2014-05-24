@echo off

IF [%1]==[] GOTO :help

haxelib run refactor convert --exclude-string-literals %1 [.]hx$ %1 /// haxe_beauty.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
