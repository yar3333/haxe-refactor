@echo off

IF [%2]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
haxelib run refactor convertFile --exclude-string-literals "%1" "%2" /[.]ts$/.hx/ %~dp0..\rules\ts_to_haxe.rules
goto exit

:dir
haxelib run refactor convert --exclude-string-literals "%1" *.ts "%2" /[.]ts$/.hx/ %~dp0..\rules\ts_to_haxe.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
