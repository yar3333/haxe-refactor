@echo off

IF [%2]==[] GOTO :help

haxelib run refactor convert --exclude-string-literals --exclude-comments "%1" *.hx "%2" /[.]hx$/.cpp/ haxe_to_cpp.rules
goto exit

:help
echo Using: %~n0 ^<srcDir^> ^<destDir^>
echo.

:exit
