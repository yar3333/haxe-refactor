# refactor #

A tool to search&replace by regex in many files, refactor haxe code and convert source code to/from haxe.

* Tip: you can use http://regex.haqteam.com/ to test your conversion rules.

### Features ###

* Massive search&replace in files
* Rename package
* Rename class
* Move class to another package
* Apply set of regex search&replace rules to files

### Predefined rule files ###

* c# to haxe
* js to haxe
* haxe to c++
* beauty html
* beauty haxe

### Simple search & replace ###
Replace aaa to bbb in `*.hx` files found in `mydirA` and `mydirB` folders:
```bash
haxelib run refactor replace mydirA;mydirB *.hx /aaa/bbb/
```
### Move class to another package ###
Move class `MyClass` from `oldpack` to `newpack` (`src` is a source project folder to search class files):
```bash
haxelib run refactor rename src oldpack.MyClass newpack.MyClass
```
### Apply many regex rules to files ###
Apply file cs_to_haxe.rules to the `*.cs` files found in `csharp_src` folder and save changed files as `*.hx` into `haxe_src folder`:
```bash
haxelib run refactor convert --exclude-string-literals csharp_src *.cs haxe_src /[.]cs$/.hx/ cs_to_haxe.rules
```
Examples of the rule files you can see in rules folder.

Also library folder contain `*.cmd` helpers to quickly run predefined conversions.