import haxe.unit.TestCase;
import sys.FileSystem;
import sys.io.File;

class Main extends TestCase
{
	static public function main()
	{
		var r = new haxe.unit.TestRunner();
		r.add(new Main());
		r.run();
	}
	
	function testOverload()
	{
		File.copy("assets/DatePeriod.hx-source", "assets/DatePeriod.hx");
		
		var refactor = new RefactorOverride("assets", null);
		refactor.overrideInFiles(5);
		
		var result = File.getContent("assets/DatePeriod.hx");
		FileSystem.deleteFile("assets/DatePeriod.hx");
		
		assertEquals(File.getContent("assets/DatePeriod.hx-ok"), result);
	}
}
