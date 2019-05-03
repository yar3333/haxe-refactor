@echo off
SETLOCAL

IF [%1]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
IF [%2]==[] (SET DEST=%~dpn1.php) ELSE (SET DEST=%2)
haxelib run refactor convertFile --exclude-string-literals --exclude-comments "%1" "%DEST%" %~dp0..\rules\java_to_php.rules
haxelib run refactor processFile --exclude-string-literals "%DEST%" %~dp0..\rules\java_to_php-comments.rules
haxelib run refactor processFile --exclude-comments        "%DEST%" %~dp0..\rules\java_to_php-strings.rules
haxelib run refactor processFile                           "%DEST%" %~dp0..\rules\java_to_php-file.rules
goto exit

:dir
IF [%2]==[] GOTO :help
haxelib run refactor convert     "%1" *.java "%2" /[.]java$/.php/ %~dp0..\rules\java_to_php.rules
haxelib run refactor prtocess -exclude-string-literals "%2" *.php %~dp0..\rules\java_to_php-comments.rules
haxelib run refactor prtocess -exclude-comments        "%2" *.php %~dp0..\rules\java_to_php-strings.rules
haxelib run refactor prtocess                          "%2" *.php %~dp0..\rules\java_to_php-file.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
ENDLOCAL
