package js;

/**
 * My_JS_Doc
 * And Second line
 */
@:native("THREE.ColladaLoader")
extern class ColladaLoader extends BaseInt
	implements Abc
{
	var animations : Array<Dynamic>;
	/**
	 * My_JS_Doc
	 * And Second line
	 */
	var kinematics : Dynamic;
	var scene : Scene;

	/**
	 * My_JS_Doc
	 * And Second line
	 */
	function new(?a:Float) : Void;
	function load(url:String, onLoad:ColladaModel->Void, onProgress:ProgressEvent->Void) : Void;
	function setCrossOrigin(value:Dynamic) : Void;
	/**
	 * My_JS_Doc
	 * And Second line
	 */
	function parse(text:String) : ColladaModel;
}