IF EXIST "dist" (
	rmdir /s /q dist
)

mkdir dist

nekotools boot library\run.n
IF NOT EXIST "library\run.exe" (
	exit /b 2
)

mv library\run.exe dist\refactor.exe

xcopy library\hant-windows.ndll dist

xcopy %NEKOPATH%\gcmt-dll.dll dist
xcopy %NEKOPATH%\neko.dll dist
xcopy %NEKOPATH%\regexp.ndll dist
xcopy %NEKOPATH%\std.ndll dist

echo D|xcopy /E library\rules dist\rules
echo D|xcopy /E library\scripts dist\scripts
