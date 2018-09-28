package js.babylon;

extern interface Behavior<T:Node>
{
	var name : String;

	function init() : Void;
	function attach(node:T) : Void;
	function detach() : Void;
}