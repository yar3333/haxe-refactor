# refactor #

A tool to search&replace by regex in many files, refactor haxe code and convert source code to/from haxe.


### Features ###

* Massive search&replace in files
* Rename package
* Rename class
* Move class to another package
* Apply set of regex search&replace rules to files
* Automatically fix overriding/overloading


### Predefined rule files ###

* ActionScript 2 to Haxe
* C# to Haxe
* Haxe to C++
* Haxe to TypeScript
* JavaScript to Haxe
* PHP to Haxe
* TypeScript to Haxe
* beauty C++
* beauty HTML
* beauty Haxe


### Simple search & replace ###
Replace **aaa** to **bbb** in `*.hx` files found in *mydirA* and *mydirB* folders:
```bash
haxelib run refactor replace mydirA;mydirB *.hx /aaa/bbb/
```


### Move class to another package ###

Move class `MyClass` from `oldpack` to `newpack` (*src* is a source project folder to search class files):
```bash
haxelib run refactor rename src oldpack.MyClass newpack.MyClass
```


### Apply many regex rules to files ###

Apply file cs_to_haxe.rules to the `*.cs` files found in *csharp_src* folder and save changed files as `*.hx` into *haxe_src* folder:
```bash
haxelib run refactor convert --exclude-string-literals csharp_src *.cs haxe_src /[.]cs$/.hx/ cs_to_haxe.rules
```
Examples of the rule files you can see in **rules** folder.

* Tip: you can use http://regex.haqteam.com/ to test your conversion rules.


### Convert code from language X to language Y ###

Just use `*.cmd` helpers to run predefined conversions (placed in **scripts** folder).
These helpers can be executed directly or through haxelib. Examples:
```bash
haxelib run refactor php_to_haxe_code MyClass.php
haxelib run refactor php_to_haxe_extern MyClass.php DestClass.hx
haxelib run refactor js_to_haxe MyClass.js
```


### Beauty code on language X ###

Just use `*.cmd` helpers to run predefined conversions (placed in **scripts** folder).
These helpers can be executed directly or through haxelib. Examples:
```bash
haxelib run refactor beauty_haxe srcDir
haxelib run refactor beauty_haxe MyClass.hx
haxelib run refactor beauty_html index.html
```


### All commands ###

Run `haxelib run refactor <command>` to get help about specified command:
```
replace         Recursive search&replace by regex in files.
replaceInFile   Search&replace by regex in specified file.
replaceInText   Like replaceInFile, but read from stdin and write to stdout.
rename          Rename haxe package or class.
convert         Massive apply regexes to files and save into other files.
convertFile     Massive apply regexes to file and save into other file.
process         Shortcut for "convert" for changing in-place.
processFile     Shortcut for "convertFile" for changing in-place.
processText     Like processFile, but read from stdin and write to stdout.
extract         Search in files and save found texts into separate files.
override        Autofix override/overload/redefinition in haxe code.
overloadInFile  Autofix overload/redefinition in haxe code.
reindent        Recursive change indentation in files.
reindentFile    Change indentation in specified file.
reindentText    Like reindentFile, but read from stdin and write to stdout.
renameFiles     Rename files recursively by regex.
lineEndings     Recursive fix line endings in files.
```


### All scripts ###

Scripts are just a `*.cmd` files in `scripts` directory to comfortable call `convert` and `process` commands with a predefined rules.
Run `haxelib run refactor <script>` to get help about specified script:
```
as2_to_haxe          Convert ActionScript 2 code into Haxe code.
beauty_cpp           Beauty C++ code files.
beauty_haxe          Beauty Haxe code files.
beauty_html          Beauty HTML code files.
cs_to_haxe           Convert C# code into Haxe code.
dts_to_haxe          Convert TypeScript definitions into Haxe externals.
haxe_to_cpp          Convert Haxe code into C++ code.
haxe_to_ts           Convert Haxe code into TypeScript code.
js_to_haxe           Convert JavaScript code into Haxe code.
php_to_haxe_code     Convert PHP code into Haxe code.
php_to_haxe_extern   Convert PHP code into Haxe externs.
php_to_haxe_typedef  Convert table of properties from php.net site into Haxe typedef.
ts_to_haxe           Convert TypeScript code into Haxe code.
```
