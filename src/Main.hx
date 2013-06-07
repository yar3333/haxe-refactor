
import hant.FileSystemTools;
import hant.Log;
import hant.PathTools;
import neko.Lib;
import sys.FileSystem;
import sys.io.File;
import sys.io.Process;
using StringTools;

class Main 
{
	static function main() 
	{
        var args = Sys.args();
		
		var exeDir = PathTools.path2normal(Sys.getCwd());
		if (args.length > 0)
		{
			var dir = args.pop();
			try
			{
				Sys.setCwd(dir);
			}
			catch (e:Dynamic)
			{
				fail("Error: could not change dir to '" + dir + "'.");
			}
		}
        
		if (args.length > 0)
		{
			var log = new Log(5);
			var fs = new FileSystemTools(log, exeDir + "/hant-" + Sys.systemName().toLowerCase());
			
			switch (args.shift())
			{
				case "replace":
					if (args.length >= 4 && args.length % 2 == 0)
					{
						var baseDir = args.shift();
						var filter = args.shift();
						
						var refactor = new Refactor(log, fs, baseDir);
						
						var rules = [];
						while (args.length > 0)
						{
							rules.push( { search:args.shift(), replacement:args.shift() } );
						}
						
						if (refactor.checkRules(rules))
						{
							refactor.replaceInFiles(new EReg(filter, "i"), rules);
						}
					}
					else
					{
						fail("Wrong arguments count.");
					}
				
				case "rename":
					if (args.length == 3)
					{
						var baseDir = args.shift();
						var src = args.shift();
						var dest = args.shift();
						
						var packs = src.split(".");
						
						if (~/^[a-z]/.match(packs[packs.length - 1]))
						{
							new Refactor(log, fs, baseDir).renamePackage(src, dest);
						}
						else
						if (~/^[A-Z]/.match(packs[packs.length - 1]))
						{
							new Refactor(log, fs, baseDir).renameClass(new ClassPath(src), new ClassPath(dest));
						}
						else
						{
							fail("Unrecognized '" + src + "'.");
						}
					}
					else
					{
						fail("Wrong arguments count.");
					}
				
				default:
					fail("Unknow command.");
			}
		}
		else
		{
			Lib.println("hxRefactor is a refactoring and search/replace tool.");
			Lib.println("Usage: haxelib run hxRefactor <command>");
			Lib.println("where <command>:");
			Lib.println("");
			Lib.println("    replace                         Recursive find and replace in files.");
			Lib.println("        <baseDirs>                  Paths to base folders. Use ';' as delimiter.");
			Lib.println("                                    Use '*' to specify 'any folder' in path.");
			Lib.println("        <filter>                    File path's filter (regular expression).");
			Lib.println("        <search> <replacement>      Regex to find and string to replace.");
			Lib.println("                                    In <replacement> use $1-$9 to substitute groups.");
			Lib.println("                                    Use '^' and 'v' between '$' and number to make uppercase/lowercase (like '$^1').");
			Lib.println("                                    Use '$-' as <replacement> to specify empty string.");
			Lib.println("        [ <search> <replacement> ]  ...");
			Lib.println("        ...                         ...");
			Lib.println("");
			Lib.println("    rename                          Rename package or class.");
			Lib.println("        <baseDir>                   Path to source folder.");
			Lib.println("        <src>                       Source package or full class name.");
			Lib.println("        <dest>                      Destination package or full class name.");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run hxRefactor replace \"src\" \"[.]hx$\" \"abc\" \"def\"");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        Only haxe code files will be processed.");
			Lib.println("        String 'abc' will be replaced to 'def'.");
			Lib.println("");
			Lib.println("    haxelib run hxRefactor replace \"*/src;*/library\" \"[.](hx|xml)$\" \"(.)bc\" \"$^1ef\"");
			Lib.println("        Files will be recursively found in 'anydir/src' and 'anydir/library' folders.");
			Lib.println("        Haxe code and xml files will be processed.");
			Lib.println("        Next strings will be replaced:");
			Lib.println("            abc => Aef");
			Lib.println("            .bc => .ef");
			Lib.println("            ...");
			Lib.println("");
			Lib.println("    haxelib run hxRefactor rename \"src\" \"mypackA.mypackB\" \"mypackC.mypackD\"");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        All classes found in the package 'mypackA.mypackB' will be moved to the package 'mypackC.mypackD'.");
			Lib.println("");
			Lib.println("    haxelib run hxRefactor rename \"src\" \"mypackA.MyClass1\" \"mypackB.MyClass2\"");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        Class 'mypackA.MyClass1' will be renamed to 'mypackB.MyClass2'.");
			Lib.println("");
		}
		
		Sys.exit(0);
	}
	
	static function fail(message:String)
	{
		Lib.println("ERROR: " + message);
		Sys.exit(1);
	}
}