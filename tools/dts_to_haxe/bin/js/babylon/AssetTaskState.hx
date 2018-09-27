package js.babylon;

@:native("THREE.BABYLON.AssetTaskState")
@:enum abstract AssetTaskState(Dynamic)
{
	/**
	 * Initialization
	 */
	var INIT = 0;
	/**
	 * Running
	 */
	var RUNNING = 1;
	/**
	 * Done
	 */
	var DONE = 2;
	/**
	 * Error
	 */
	var ERROR = 3;
}