package js;

@:native("THREE.MyStrings")
extern class MyStrings
{
	var myVar1 : haxe.extern.EitherType<js.mystrings.MyVar1, Float>;
	var myVar2 : haxe.extern.EitherType<Float, String>;
}