@echo off

IF [%2]==[] GOTO :help

haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.as "%2" /[.]as$/.hx/ as2_to_haxe.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^> ^<destDir^>
echo.

:exit
