@echo off

IF [%2]==[] GOTO :help
IF EXIST %1\NUL GOTO :dir

:file
haxelib run refactor convertFile --exclude-string-literals --exclude-comments "%1" "%2" /[.]hx$/.cpp/ %~dp0..\rules\haxe_to_cpp.rules
goto exit

:dir
haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.hx "%2" /[.]hx$/.cpp/ %~dp0..\rules\haxe_to_cpp.rules
goto exit

:help
echo Using: %~n0 ^<src^> ^<dest^>
echo.

:exit
