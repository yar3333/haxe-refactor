
import hant.FileSystemTools;
import hant.Log;
import hant.PathTools;
import neko.Lib;
import sys.FileSystem;
import sys.io.File;
import sys.io.Process;
using StringTools;
using Lambda;

class Main 
{
	static function main() 
	{
        var args = Sys.args();
		
		var exeDir = PathTools.normalize(Sys.getCwd());
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
			
			var verbose = false;
			
			var k = args.shift();
			if (k == "-v")
			{
				verbose = true;
				k = args.shift();
			}
			
			switch (k)
			{
				case "replace":
					if (args.length >= 4 && args.length % 2 == 0)
					{
						var baseDir = args.shift();
						var filter = args.shift();
						
						var refactor = new Refactor(log, fs, baseDir, null, verbose);
						
						var rules = [];
						while (args.length > 0)
						{
							rules.push(new Rule(args.shift()));
						}
						
						if (refactor.checkRules(rules))
						{
							refactor.replaceInFiles(new EReg(filter, "i"), new Rule("///"), rules);
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
							new Refactor(log, fs, baseDir, null, verbose).renamePackage(src, dest);
						}
						else
						if (~/^[A-Z]/.match(packs[packs.length - 1]))
						{
							new Refactor(log, fs, baseDir, null, verbose).renameClass(new ClassPath(src), new ClassPath(dest));
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
					
				case "convert":
					if (args.length == 5)
					{
						var baseDir = args.shift();
						var filter = args.shift();
						var outDir = args.shift();
						var changeFileName = new Rule(args.shift());
						var rulesFile = args.shift();
						
						if (FileSystem.exists(rulesFile))
						{
							var refactor = new Refactor(log, fs, baseDir, outDir, verbose);
							var lines = File.getContent(rulesFile).replace("\r", "").split("\n");
							var consts = new Array<{ name:String, value:String }>();
							var rules = new Array<Rule>();
							for (line in lines)
							{
								if (~/^\s*\/\//.match(line)) continue;
								
								var n = line.indexOf("=");
								if (n > 0 && ~/\s*[_a-zA-Z][_a-zA-Z0-9]*\s*[=]/.match(line))
								{
									var name = line.substr(0, n).trim();
									var value = line.substr(n + 1).trim();
									if (name.length > 0 && value.length > 0)
									{
										for (const in consts)
										{
											value = value.replace(const.name, const.value);
										}
										consts.push({ name:name, value:value });
									}
									else
									{
										if (line.trim().length > 0)
										{
											fail("Error in line '" + line + "'.");
										}
									}
								}
								else
								{
									line = line.trim();
									if (line.length > 0)
									{
										for (const in consts)
										{
											line = line.replace(const.name, const.value);
										}
										rules.push(new Rule(line));
									}
								}
							}
							if (refactor.checkRules(rules))
							{
								refactor.replaceInFiles(new EReg(filter, "i"), changeFileName, rules);
							}
						}
						else
						{
							fail("File '" + rulesFile + "' is not found.");
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
			Lib.println("Refactor is a refactoring and search/replace tool.");
			Lib.println("Usage: haxelib run refactor [-v] <command>");
			Lib.println("where '-v' is the verbose key and <command> may be:");
			Lib.println("");
			Lib.println("    replace                         Recursive find and replace in files.");
			Lib.println("        <baseDirs>                  Paths to base folders. Use ';' as delimiter.");
			Lib.println("                                    Use '*' to specify 'any folder' in path.");
			Lib.println("        <filter>                    File path's filter (regular expression).");
			Lib.println("        /search/replacement/flags   Regex to find and replace.");
			Lib.println("                                    In <replacement> use $1-$9 to substitute groups.");
			Lib.println("                                    Use '^' and 'v' between '$' and number to make uppercase/lowercase (like '$^1').");
			Lib.println("        ...                         More regexs.");
			Lib.println("");
			Lib.println("    rename                          Rename package or class.");
			Lib.println("        <baseDir>                   Path to source folder.");
			Lib.println("        <src>                       Source package or full class name.");
			Lib.println("        <dest>                      Destination package or full class name.");
			Lib.println("");
			Lib.println("    convert                         Recursive find and replace in files using rules file.");
			Lib.println("        <baseDir>                   Path to source folder.");
			Lib.println("        <filter>                    File path's filter (regular expression).");
			Lib.println("        <outDir>                    Output directory.");
			Lib.println("        /search/replacement/flags   Regex to find and replace in file name.");
			Lib.println("                                    Used to produce output file name.");
			Lib.println("        <rulesFile>                 Path to rules file, one rule per line:");
			Lib.println("                                    VAR = regexp");
			Lib.println("                                    or");
			Lib.println("                                    /search_can_contain_VAR/replacement/flags");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor replace src [.]hx$ /abc/def/");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        Only haxe code files will be processed.");
			Lib.println("        String 'abc' will be replaced to 'def'.");
			Lib.println("");
			Lib.println("    haxelib run refactor replace */src;*/library [.](hx|xml)$ /(.)bc/$^1ef/");
			Lib.println("        Files will be recursively found in 'anydir/src' and 'anydir/library' folders.");
			Lib.println("        Haxe code and xml files will be processed.");
			Lib.println("        Next strings will be replaced:");
			Lib.println("            abc => Aef");
			Lib.println("            .bc => .ef");
			Lib.println("            ...");
			Lib.println("");
			Lib.println("    haxelib run refactor rename src mypackA.mypackB mypackC.mypackD");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        All classes found in the package 'mypackA.mypackB' will be moved to the package 'mypackC.mypackD'.");
			Lib.println("");
			Lib.println("    haxelib run refactor rename src mypackA.MyClass1 mypackB.MyClass2");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        Class 'mypackA.MyClass1' will be renamed to 'mypackB.MyClass2'.");
			Lib.println("");
			Lib.println("    haxelib run refactor convert native [.]js$ src /[.]js$/.hx/ convert.rules");
			Lib.println("        Search for *.js files in the 'native' folder.");
			Lib.println("        Put output files as '*.hx' into the 'src' folder.");
			Lib.println("        Read rules from rules.txt, for example:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*");
			Lib.println("            ARGS = (?:\\s*ID\\s*(?:,\\s*ID\\s*)*)?");
			Lib.println("            /^(SPACE)\\bvar\\s+_(ID)\\s*=\\s*function\\s*[(](ARGS)[)]\\s*$/$1function _$2($3)/m");
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