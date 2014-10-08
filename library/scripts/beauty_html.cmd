@echo off

IF [%1]==[] GOTO :help

haxelib run refactor process "%1" *.html beauty_html.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
