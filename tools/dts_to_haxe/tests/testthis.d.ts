declare module BABYLON {
	class TestThis {
        getThis(): this;
	}
	
    interface ITestThis {
        getThis(): this;
    }
}