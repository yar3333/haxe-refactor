import hant.CmdOptions;
import hant.FileSystemTools;
import hant.Log;
import hant.PathTools;
import neko.Lib;
import stdlib.Regex;
import sys.FileSystem;
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
			var fs = new FileSystemTools(log);
			
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
					if (args.length >= 3)
					{
						var baseDir = args.shift();
						var filter = filterToRegex(args.shift());
						
						var refactor = new Refactor(log, fs, baseDir, null, verbose);
						
						var rules = [];
						while (args.length > 0)
						{
							rules.push(new Regex(args.shift()));
						}
						
						if (refactor.checkRules(rules))
						{
							refactor.replaceInFiles(new EReg(filter, "i"), new Regex("///"), rules, false, false);
						}
					}
					else
					{
						fail("Wrong arguments count.");
					}
				case "replaceInFile":
					if (args.length >= 2)
					{
						var filePath = args.shift();
						
						var refactor = new Refactor(log, fs, null, null, verbose);
						
						var rules = [];
						while (args.length > 0)
						{
							rules.push(new Regex(args.shift()));
						}
						
						if (refactor.checkRules(rules))
						{
							refactor.replaceInFile(filePath, rules, filePath, false, false);
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
						var src = pathToPack(baseDir, args.shift());
						var dest = pathToPack(baseDir, args.shift());
						
						var srcPacks = src.split(".");
						if (~/^[a-z]/.match(srcPacks[srcPacks.length - 1]))
						{
							new Refactor(log, fs, baseDir, null, verbose).renamePackage(src, dest);
						}
						else
						if (~/^[A-Z]/.match(srcPacks[srcPacks.length - 1]))
						{
							var destPacks = dest.split(".");
							if (!(~/^[A-Z]/.match(destPacks[destPacks.length - 1])))
							{
								var n = src.lastIndexOf(".");
								n = n < 0 ? 0 : n + 1;
								dest += "." + src.substr(n);
							}
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
					var options = new CmdOptions();
					
					options.add("baseDir", "");
					options.add("filter", "");
					options.add("outDir", "");
					options.add("changeFileName", "");
					options.add("rulesFile", "");
					options.add("excludeStrings", false, [ "--exclude-string-literals" ]);
					options.add("excludeComments", false, [ "--exclude-comments" ]);
					
					options.parse(args);
					
					var baseDir = options.get("baseDir");
					var filter = filterToRegex(options.get("filter"));
					var outDir = options.get("outDir");
					var changeFileName = options.get("changeFileName") != "" ? new Regex(options.get("changeFileName")) : null;
					var rulesFile = options.get("rulesFile");
					var excludeStrings = options.get("excludeStrings");
					var excludeComments = options.get("excludeComments");
					
					if (baseDir == "") fail("<baseDir> arg must be specified.");
					if (filter == "") fail("<filter> arg must be specified.");
					if (outDir == "") fail("<outDir> arg must be specified.");
					if (changeFileName == null) fail("<changeFileName> arg must be specified.");
					if (rulesFile == "") fail("<rulesFile> arg must be specified.");
					
					if (!FileSystem.exists(rulesFile))
					{
						var altRulesFile = haxe.io.Path.join([ exeDir, "rules", rulesFile ]);
						if (FileSystem.exists(altRulesFile) && !FileSystem.isDirectory(altRulesFile))
						{
							rulesFile = altRulesFile;
						}
					}
					
					if (!FileSystem.exists(rulesFile)) fail("Could't find rulesFile '" + rulesFile + "'.");
					
					new Convert(log, fs, verbose, rulesFile).process(baseDir, filter, outDir, changeFileName, excludeStrings, excludeComments);
					
				case "reindent":
					if (args.length == 6)
					{
						var baseDir = args.shift();
						var filter = filterToRegex(args.shift());
						
						var oldTabSize = Std.parseInt(args.shift());
						var oldIndentSize = Std.parseInt(args.shift());
						
						var newTabSize = Std.parseInt(args.shift());
						var newIndentSize = Std.parseInt(args.shift());
						
						var refactor = new Refactor(log, fs, baseDir, null, verbose);
						refactor.reindent(new EReg(filter, "i"), oldTabSize, oldIndentSize, newTabSize, newIndentSize);
					}
					else
					if (args.length == 5)
					{
						var filePath = args.shift();
						
						var oldTabSize = Std.parseInt(args.shift());
						var oldIndentSize = Std.parseInt(args.shift());
						
						var newTabSize = Std.parseInt(args.shift());
						var newIndentSize = Std.parseInt(args.shift());
						
						var refactor = new Refactor(log, fs, null, null, verbose);
						refactor.reindentFile(filePath, oldTabSize, oldIndentSize, newTabSize, newIndentSize);
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
			Lib.println("        <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("        /search/replacement/flags   Regex to find and replace.");
			Lib.println("                                    In <replacement> use $1-$9 to substitute groups.");
			Lib.println("                                    Use '^' and 'v' between '$' and number to make uppercase/lowercase (like '$^1').");
			Lib.println("        ...                         More regexs.");
			Lib.println("");
			Lib.println("    replaceInFile                   Find and replace in file.");
			Lib.println("        <filePath>                  Path to file.");
			Lib.println("        /search/replacement/flags   Regex to find and replace.");
			Lib.println("        ...                         More regexs.");
			Lib.println("");
			Lib.println("    rename                          Rename package or class.");
			Lib.println("        <baseDir>                   Path to source folder.");
			Lib.println("        <src>                       Source package or full class name.");
			Lib.println("        <dest>                      Destination package or full class name.");
			Lib.println("");
			Lib.println("    convert                         Recursive find and replace in files using rules file.");
			Lib.println("        --exclude-string-literals   Exclude C-like strings from process.");
			Lib.println("        --exclude-comments          Exclude C-like comments from process.");
			Lib.println("        <baseDir>                   Path to source folder.");
			Lib.println("        <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("        <outDir>                    Output directory.");
			Lib.println("        /search/replacement/flags   Regex to find and replace in file name.");
			Lib.println("                                    Used to produce output file name.");
			Lib.println("        <rulesFile>                 Path to rules file, one rule per line:");
			Lib.println("                                    VAR = regexp");
			Lib.println("                                    or");
			Lib.println("                                    /search_can_contain_VAR/replacement/flags");
			Lib.println("");
			Lib.println("    reindent                        Change indentation in the files.");
			Lib.println("        <baseDirs>                  Paths to source folders.");
			Lib.println("        <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("                                    If you want quickly process only one file");
			Lib.println("                                    then specify <filePath> instead of <baseDirs> and <filter>.");
			Lib.println("        <oldTabSize>                Spaces per tab in old style.");
			Lib.println("        <oldIndentSize>             Spaces per indent in old style.");
			Lib.println("        <newTabSize>                Spaces per tab in new style.");
			Lib.println("        <newIndentSize>             Spaces per indent in new style.");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor replace src *.hx /abc/def/");
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
			Lib.println("    haxelib run refactor convert native *.js src /[.]js$/.hx/ js_to_haxe.rules");
			Lib.println("        Search for *.js files in the 'native' folder.");
			Lib.println("        Put output files as '*.hx' into the 'src' folder.");
			Lib.println("        Read rules from file 'js_to_haxe.rules'. Rules example:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*        # define a var");
			Lib.println("            ARGS = (?:\\s*ID\\s*(?:,\\s*ID\\s*)*)? # define a var");
			Lib.println("            SPACE = [ \\t\\r\\n]                  # define a var");
			Lib.println("            # regex to find&replace");
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
	
	static function pathToPack(srcDirs:String, path:String) : String
	{
		path = PathTools.normalize(path);
		if (path.indexOf("/") < 0 && !path.endsWith(".hx")) return path;
		
		if (path.endsWith(".hx")) path = path.substr(0, path.length - ".hx".length);
		
		for (srcDir in srcDirs.split(";").map(function(e) return PathTools.normalize(e)))
		{
			if (path.startsWith(srcDir + "/"))
			{
				return path.substr(srcDir.length + 1).replace("/", ".");
			}
		}
		
		fail("Path '" + path + "' is not in source directories.");
		return null;
	}
	
	static function filterToRegex(s:String) : String
	{
		if (~/^[*][.][a-z0-9_-]+(?:\s*;\s*[*][.][a-z0-9_-]+)*$/i.match(s))
		{
			var exts = s.split(";").map(function(s) return s.trim().substr("*.".length));
			return "[.](?:" + exts.join("|") + ")$";
		}
		return s;
	}
}
