@echo off

IF [%1]==[] GOTO :help

haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.hx "%1" /// beauty_haxe.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^>
echo.

:exit
