@echo off

IF [%1]==[] GOTO :help

haxelib run refactor convert "%1" *.html "%1" /// beauty_html.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
