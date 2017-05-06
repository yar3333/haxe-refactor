/**
 * My_JS_Doc
 * And Second line
 */
class ColladaLoader extends BaseInt
	implements Abc
{
	public var animations : Array<Dynamic>;
	/**
	 * My_JS_Doc
	 * And Second line
	 */
	public var kinematics : Dynamic;
	public var scene : Scene;

	/**
	 * My_JS_Doc
	 * And Second line
	 */
	public function new() : Void;

	public function load(url:String, onLoad:ColladaModel->Void, onProgress:ProgressEvent->Void) : Void;

	public function setCrossOrigin(value:Dynamic) : Void;

	/**
	 * My_JS_Doc
	 * And Second line
	 */
	public function parse(text:String) : ColladaModel;
}