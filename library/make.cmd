@del library.zip 2>NUL
7z a -tzip library.zip * -xr!make.cmd -xr!library.zip -xr!ndll\Windows\*.exp -xr!ndll\Windows\*.lib
haxelib submit library.zip
@pause
