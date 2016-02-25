@echo off

IF [%2]==[] GOTO :help

php "%~dp0php_to_haxe.php" code "%1" "%2"
haxelib run refactor processFile --exclude-string-literals --exclude-comments "%2" php_to_haxe.rules
haxelib run refactor processFile --exclude-string-literals --exclude-comments "%2" php_to_haxe_code.rules
goto exit

:help
echo Using: %~n0 ^<srcFile.php^> ^<destFile.hx^>
echo.

:exit
