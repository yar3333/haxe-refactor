@echo off

IF [%2]==[] GOTO :help

haxelib run refactor convertFile "%1" "%2" php_to_haxe_typedef.rules
goto exit

:help
echo Using: %~n0 ^<srcFile.php^> ^<destFile.hx^>
echo.

:exit
