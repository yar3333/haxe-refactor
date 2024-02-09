import utest.Assert;
import utest.Runner;
import sys.FileSystem;
import sys.io.File;

class Main extends utest.Test
{
	static public function main()
	{
		var r = new Runner();
		r.addCase(new Main());
		r.run();
	}
	
	function testOverload()
	{
		File.copy("assets/DatePeriod.hx-source", "assets/DatePeriod.hx");
		
		var refactor = new RefactorOverride("assets", null);
		refactor.overrideInFiles(5);
		
		var result = File.getContent("assets/DatePeriod.hx");
		FileSystem.deleteFile("assets/DatePeriod.hx");
		
		Assert.equals(File.getContent("assets/DatePeriod.hx-ok"), result);
	}
}
