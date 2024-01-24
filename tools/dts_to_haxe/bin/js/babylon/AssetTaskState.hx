package js.babylon;

@:native("THREE.BABYLON.AssetTaskState")
extern enum AssetTaskState
{
	/**
	 * Initialization
	 */
	INIT = 0;
	/**
	 * Running
	 */
	RUNNING = 1;
	/**
	 * Done
	 */
	DONE = 2;
	/**
	 * Error
	 */
	ERROR = 3;
}