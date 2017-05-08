package js;

@:native("THREE.Curve")
extern class Curve<T:Vector>
{
	var width : Int;
	var height : String;
	var arr : Array<Int>;
	var arr2 : Array<Int>;

	function test(width:Int, height:String) : Void;
}