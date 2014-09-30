@echo off

IF [%2]==[] GOTO :help

haxelib run refactor convert --exclude-string-literals "%1" *.ts "%2" /[.]ts$/.hx/ ts_to_haxe.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^> ^<destDir^>
echo.

:exit
