package js;

@:native("THREE.Detector")
extern class Detector
{
	static var canvas : Bool;
	static var webgl : Bool;
	static var workers : Bool;
	static var fileapi : Bool;

	static function getWebGLErrorMessage() : HTMLElement;
	static function addGetWebGLMessage(parameters:{ @:optional var id : String; @:optional var parent : HTMLElement; }) : Void;
	static function testDots(parameters:Float) : Void;
}