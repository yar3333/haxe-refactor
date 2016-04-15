@echo off

IF [%2]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
haxelib run refactor convertFile --exclude-string-literals --exclude-comments "%1" "%2" /[.]js$/.hx/ %~dp0..\rules\js_to_haxe.rules
goto exit

:dir
haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.js "%2" /[.]js$/.hx/ %~dp0..\rules\js_to_haxe.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
