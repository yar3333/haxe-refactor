@echo off

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
haxelib run refactor process "%1" %~dp0..\rules\beauty_html.rules
goto exit

:dir
haxelib run refactor process "%1" *.html %~dp0..\rules\beauty_html.rules
goto exit

:help
echo Using: %~n0 ^<src^>
echo.

:exit
