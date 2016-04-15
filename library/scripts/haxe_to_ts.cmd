@echo off

IF [%2]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
haxelib run refactor convertFile --exclude-string-literals --exclude-comments "%1" "%2" /[.]hx$/.ts/ %~dp0..\rules\haxe_to_ts.rules
goto exit

:dir
haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.hx "%2" /[.]hx$/.ts/ %~dp0..\rules\haxe_to_ts.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
