package js;

@:native("THREE.ColladaLoaderReturnType")
extern interface ColladaLoaderReturnType
	extends BaseInt
	extends Abc
{
	var myfield : haxe.extern.EitherType<Float, String>;

	function myfunc(a:Float, b:Dynamic) : Void;
}