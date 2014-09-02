@echo off

IF [%1]==[] GOTO :help

haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.c;*.cpp;*.h "%1" /// beauty_cpp.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
