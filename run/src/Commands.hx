import hant.CmdOptions;
import hant.FileSystemTools;
import hant.Log;
import neko.Lib;
import stdlib.Regex;
using StringTools;
using Lambda;

class Commands extends BaseCommands
{
	var log : Log;
	var fs : FileSystemTools;
	var verbose : Bool;
	var exeDir : String;
	
	public function new(log:Log, fs:FileSystemTools, verbose:Bool, exeDir:String) 
	{
		this.log = log;
		this.fs = fs;
		this.verbose = verbose;
		this.exeDir = exeDir;
	}
	
	public function replace(args:Array<String>)
	{
		if (args.length >= 3)
		{
			var baseDir = args.shift();
			var filter = filterToRegex(args.shift());
			
			var refactor = new RefactorReplace(log, fs, baseDir, null, verbose);
			
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
		if (args.length == 0)
		{
			Lib.println("Recursive find and replace in files.");
			Lib.println("Usage: haxelib run refactor [-v] replace <baseDirs> <filter> <regex1> [ ... <regexN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <baseDirs>                  Paths to base folders. Use ';' as delimiter.");
			Lib.println("                                Use '*' to specify 'any folder' in path.");
			Lib.println("    <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <regex>                     Regex to find and replace (/search/replacement/flags).");
			Lib.println("                                In <replacement> use $1-$9 to substitute groups.");
			Lib.println("                                Use '^' and 'v' between '$' and number to make uppercase/lowercase (like '$^1').");
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
			
		}
		else
		{
			fail("Wrong arguments count.");
		}
	}
	
	public function replaceInFile(args:Array<String>)
	{
		if (args.length >= 2)
		{
			var filePath = args.shift();
			
			var refactor = new RefactorReplace(log, fs, null, null, verbose);
			
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
		if (args.length == 0)
		{
			Lib.println("Find and replace in file.");
			Lib.println("Usage: haxelib run refactor [-v] replaceInFile <filePath> <regex1> [ ... <regexN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <filePath>                  Path to file.");
			Lib.println("    <regex>                     Regex to find and replace (/search/replacement/flags).");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor replaceInFile src/MyClass.hx /abc/def/i");
		}
		else
		{
			fail("Wrong arguments count.");
		}
	}
	
	public function rename(args:Array<String>)
	{
		if (args.length == 3)
		{
			var baseDir = args.shift();
			var src = pathToPack(baseDir, args.shift());
			var dest = pathToPack(baseDir, args.shift());
			
			var srcPacks = src.split(".");
			if (~/^[a-z]/.match(srcPacks[srcPacks.length - 1]))
			{
				new RefactorRename(log, fs, baseDir, null, verbose).renamePackage(src, dest);
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
				new RefactorRename(log, fs, baseDir, null, verbose).renameClass(new ClassPath(src), new ClassPath(dest));
			}
			else
			{
				fail("Unrecognized '" + src + "'.");
			}
		}
		else
		if (args.length == 0)
		{
			Lib.println("Rename package or class.");
			Lib.println("Usage: haxelib run refactor [-v] rename <baseDir> <src> <dest>");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <baseDir>                   Path to source folder.");
			Lib.println("    <src>                       Source package or full class name.");
			Lib.println("    <dest>                      Destination package or full class name.");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor rename src mypackA.mypackB mypackC.mypackD");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        All classes found in the package 'mypackA.mypackB' will be moved to the package 'mypackC.mypackD'.");
			Lib.println("");
			Lib.println("    haxelib run refactor rename src mypackA.MyClass1 mypackB.MyClass2");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        Class 'mypackA.MyClass1' will be renamed to 'mypackB.MyClass2'.");
		}
		else
		{
			fail("Wrong arguments count.");
		}
	}
	
	public function convert(args:Array<String>)
	{
		if (args.length > 0)
		{
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
			
			var processor = new RegexProcessor(log, fs, verbose, getRulesFilePath(exeDir, rulesFile));
			processor.convert(baseDir, filter, outDir, changeFileName, excludeStrings, excludeComments);
		}
		else
		{
			Lib.println("Recursive find and replace in files using rules file.");
			Lib.println("Usage: haxelib run refactor [-v] convert [ --exclude-string-literals ] [ --exclude-comments ] <baseDir> <filter> <outDir> <regex> <rulesFile>");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    --exclude-string-literals   Exclude C-like strings from process.");
			Lib.println("    --exclude-comments          Exclude C-like comments from process.");
			Lib.println("    <baseDir>                   Path to source folder.");
			Lib.println("    <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <outDir>                    Output directory.");
			Lib.println("    <regex>                     Regex to find and replace in file name (/search/replacement/flags).");
			Lib.println("                                Used to produce output file name.");
			Lib.println("    <rulesFile>                 Path to rules file, one rule per line:");
			Lib.println("                                VAR = regexp");
			Lib.println("                                or");
			Lib.println("                                /search_can_contain_VAR/replacement/flags");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor convert native *.js src /[.]js$/.hx/ js_to_haxe.rules");
			Lib.println("        Search for *.js files in the 'native' folder.");
			Lib.println("        Put output files as '*.hx' into the 'src' folder.");
			Lib.println("        Read rules from file 'js_to_haxe.rules'. Rules example:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*        // define a var");
			Lib.println("            ARGS = (?:\\s*ID\\s*(?:,\\s*ID\\s*)*)? // define a var");
			Lib.println("            SPACE = [ \\t\\r\\n]                  // define a var");
			Lib.println("            // regex to find&replace");
			Lib.println("            /^(SPACE)\\bvar\\s+_(ID)\\s*=\\s*function\\s*[(](ARGS)[)]\\s*$/$1function _$2($3)/m");
			
		}
	}
	
	public function extract(args:Array<String>)
	{
		if (args.length > 0)
		{
			var options = new CmdOptions();
			
			options.add("baseDir", "");
			options.add("filter", "");
			options.add("outDir", "");
			options.add("rulesFile", "");
			
			options.parse(args);
			
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var outDir = options.get("outDir");
			var rulesFile = options.get("rulesFile");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (outDir == "") fail("<outDir> arg must be specified.");
			if (rulesFile == "") fail("<rulesFile> arg must be specified.");
			
			var processor = new RegexProcessor(log, fs, verbose, getRulesFilePath(exeDir, rulesFile));
			processor.extract(baseDir, filter, outDir);
		}
		else
		{
			Lib.println("Recursive find files and extract parts of them to separate files.");
			Lib.println("Usage: haxelib run refactor [-v] extract <baseDir> <filter> <outDir> <rulesFile>");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <baseDir>                   Path to folder.");
			Lib.println("    <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <outDir>                    Output directory.");
			Lib.println("    <rulesFile>                 Path to rules file (see 'convert' command).");
			Lib.println("                                Each regex must be in form '/find_start_text/out_file_name/flags'.");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor extract src *.hx out split_haxe.rules");
			Lib.println("        This command extract classes to separate files.");
			Lib.println("        Content of the 'split_haxe.rules' file:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*");
			Lib.println("            SPACE = [ \\t\\r\\n]");
			Lib.println("            /class(?:SPACE+)(ID)(?:SPACE+){/$1.hx/g");
		}
	}
	
	public function doOverride(args:Array<String>)
	{
		if (args.length == 1)
		{
			var srcDirs = args.shift();
			var refactor = new RefactorOverride(log, fs, srcDirs, null, verbose);
			refactor.overrideInFiles();
		}
		else
		if (args.length == 0)
		{
			Lib.println("Autofix override/overload/redefinition in haxe extern class members.");
			Lib.println("Usage: haxelib run refactor [-v] override <srcDirs>");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <srcDirs>                   Paths to sorce folders. Use ';' as delimiter.");
			Lib.println("                                Use '*' to specify 'any folder' in path.");
			Lib.println("");
			Lib.println("Example:");
			Lib.println("");
			Lib.println("    // file A.hx:");
			Lib.println("    extern class A");
			Lib.println("    {");
			Lib.println("        var v : Int;");
			Lib.println("        function f(p:Int) : Int;");
			Lib.println("     }");
			Lib.println("");
			Lib.println("    // file B.hx:");
			Lib.println("    extern class B extends A");
			Lib.println("    {");
			Lib.println("        var v : Float;");
			Lib.println("        function f(p:String) : String;");
			Lib.println("    }");
			Lib.println("");
			Lib.println("    // run command:");
			Lib.println("    haxelib run refactor override src");
			Lib.println("");
			Lib.println("    // file A.hx - nothing changed");
			Lib.println("");
			Lib.println("    // file B.hx - fixed:");
			Lib.println("    extern class B extends A");
			Lib.println("    {");
			Lib.println("        //var v : Float;");
			Lib.println("        @:overload(function(p:String):String{})");
			Lib.println("        override function f(p:Int) : Int;");
			Lib.println("    }");
			
		}
		else
		{
			fail("Wrong arguments count.");
		}
	}
	
	public function reindent(args:Array<String>)
	{
		if (args.length == 6 || args.length == 7)
		{
			var baseDir = args.shift();
			var filter = filterToRegex(args.shift());
			
			var oldTabSize = Std.parseInt(args.shift());
			var oldIndentSize = Std.parseInt(args.shift());
			
			var newTabSize = Std.parseInt(args.shift());
			var newIndentSize = Std.parseInt(args.shift());
			
			var shiftSize = args.length > 0 ? Std.parseInt(args.shift()) : 0;
			
			var refactor = new RefactorReindent(log, fs, baseDir, null, verbose);
			refactor.reindent(new EReg(filter, "i"), oldTabSize, oldIndentSize, newTabSize, newIndentSize, shiftSize);
		}
		else
		if (args.length == 0)
		{
			Lib.println("Change indentation in the files.");
			Lib.println("Usage: haxelib run refactor [-v] reindent <baseDirs> <filter> <oldTabSize> <oldIndentSize> <newTabSize> <newIndentSize> [ <shiftSize> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <baseDirs>                  Paths to folders (like 'mydirA;mydirB').");
			Lib.println("    <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <oldTabSize>                Spaces per tab in old style.");
			Lib.println("    <oldIndentSize>             Spaces per indent in old style.");
			Lib.println("    <newTabSize>                Spaces per tab in new style.");
			Lib.println("    <newIndentSize>             Spaces per indent in new style.");
			Lib.println("    <shiftSize>                 Shift to left(-) or right(+) to specified spaces.");
		}
		else
		{
			fail("Wrong arguments count.");
		}
	}
	
	public function reindentInFile(args:Array<String>)
	{
		if (args.length == 5 || args.length == 6)
		{
			var filePath = args.shift();
			
			var oldTabSize = Std.parseInt(args.shift());
			var oldIndentSize = Std.parseInt(args.shift());
			
			var newTabSize = Std.parseInt(args.shift());
			var newIndentSize = Std.parseInt(args.shift());
			
			var shiftSize = args.length > 0 ? Std.parseInt(args.shift()) : 0;
			
			var refactor = new RefactorReindent(log, fs, null, null, verbose);
			refactor.reindentFile(filePath, oldTabSize, oldIndentSize, newTabSize, newIndentSize, shiftSize);
		}
		else
		if (args.length == 0)
		{
			Lib.println("Change indentation in the file.");
			Lib.println("Usage: haxelib run refactor [-v] reindentInFile <filePath> <oldTabSize> <oldIndentSize> <newTabSize> <newIndentSize> [ <shiftSize> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <filePath>                  Path to file.");
			Lib.println("    <oldTabSize>                Spaces per tab in old style.");
			Lib.println("    <oldIndentSize>             Spaces per indent in old style.");
			Lib.println("    <newTabSize>                Spaces per tab in new style.");
			Lib.println("    <newIndentSize>             Spaces per indent in new style.");
			Lib.println("    <shiftSize>                 Shift to left(-) or right(+) to specified spaces.");
		}
		else
		{
			fail("Wrong arguments count.");
		}
	}
}