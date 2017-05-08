package js;

@:native("THREE.ColladaLoaderReturnType")
typedef ColladaLoaderReturnType =
{>BaseInt, >Abc,
	var info : { var render : { var vertices : Float; var faces : Float; }; };
	var pointMap : Dynamic<Array<Float>>;
	var myfield : haxe.extern.EitherType<Float, String>;

	function dispatchEvent(event:{ var type : String; }) : Void;
	function myfunc(a:Float, b:Dynamic) : Void;
}