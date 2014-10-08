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
			
			var rules = Rules.fromLines(args, verbose, log);
			if (rules.check())
			{
				refactor.replaceInFiles(new EReg(filter, "i"), new Regex("///"), rules.regexs, false, false);
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
			
			var rules = Rules.fromLines(args, verbose, log);
			if (rules.check())
			{
				refactor.replaceInFile(filePath, rules.regexs, filePath, false, false);
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
			
			options.add("excludeStrings", false, [ "-es", "--exclude-string-literals" ]);
			options.add("excludeComments", false, [ "-ec", "--exclude-comments" ]);
			options.add("baseDir", "");
			options.add("filter", "");
			options.add("outDir", "");
			options.add("changeFileName", "");
			options.addRepeatable("ruleFiles", String);
			
			options.parse(args);
			
			var excludeStrings = options.get("excludeStrings");
			var excludeComments = options.get("excludeComments");
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var outDir = options.get("outDir");
			var changeFileName = options.get("changeFileName") != "" ? new Regex(options.get("changeFileName")) : null;
			var ruleFiles : Array<String> = options.get("ruleFiles");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (outDir == "") fail("<outDir> arg must be specified.");
			if (changeFileName == null) fail("<changeFileName> arg must be specified.");
			if (ruleFiles.length == 0) fail("<rulesFile> arg must be specified.");
			
			var regexs = [];
			for (ruleFile in ruleFiles)
			{
				regexs = regexs.concat(Rules.fromFile(getRulesFilePath(exeDir, ruleFile), verbose, log).regexs);
			}
			var refactor = new RefactorConvert(log, fs, baseDir, outDir, verbose);
			refactor.convert(filter, changeFileName, regexs, excludeStrings, excludeComments);
		}
		else
		{
			Lib.println("Recursive find and replace in files using rule files.");
			Lib.println("Usage: haxelib run refactor [-v] convert [ --exclude-string-literals ] [ --exclude-comments ] <baseDir> <filter> <outDir> <convertFileName> <rulesFile1> [ ... <rulesFileN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    -es, --exclude-string-literals  Exclude C-like strings from search.");
			Lib.println("    -ec, --exclude-comments         Exclude C-like comments from search.");
			Lib.println("    <baseDir>                       Path to source folder.");
			Lib.println("    <filter>                        File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <outDir>                        Output directory.");
			Lib.println("    <convertFileName>               Regex to find and replace in file name (/search/replacement/flags).");
			Lib.println("                                    Used to produce output file name.");
			Lib.println("    <rulesFile>                     Path to rules file which contains one rule per line:");
			Lib.println("                                    VAR = regexp");
			Lib.println("                                    or");
			Lib.println("                                    /search_can_contain_VAR/replacement/flags");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor convert native *.js src /[.]js$/.hx/ js_to_haxe.rules");
			Lib.println("        Search for *.js files in the 'native' folder.");
			Lib.println("        Put output files as '*.hx' into the 'src' folder.");
			Lib.println("        Read rules from file 'js_to_haxe.rules'. Rules example:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*");
			Lib.println("            ARGS = (?:\\s*ID\\s*(?:,\\s*ID\\s*)*)?");
			Lib.println("            SPACE = [ \\t\\r\\n]");
			Lib.println("            /^(SPACE)\\bvar\\s+_(ID)\\s*=\\s*function\\s*[(](ARGS)[)]\\s*$/$1function _$2($3)/m");
			
		}
	}
	
	public function process(args:Array<String>)
	{
		if (args.length > 0)
		{
			var options = new CmdOptions();
			
			options.add("excludeStrings", false, [ "--exclude-string-literals" ]);
			options.add("excludeComments", false, [ "--exclude-comments" ]);
			options.add("baseDir", "");
			options.add("filter", "");
			options.addRepeatable("ruleFiles", String);
			
			options.parse(args);
			
			var excludeStrings = options.get("excludeStrings");
			var excludeComments = options.get("excludeComments");
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var ruleFiles : Array<String> = options.get("ruleFiles");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (ruleFiles.length == 0) fail("<ruleFiles> arg must be specified.");
			
			var regexs = [];
			for (ruleFile in ruleFiles)
			{
				regexs = regexs.concat(Rules.fromFile(getRulesFilePath(exeDir, ruleFile), verbose, log).regexs);
			}
			var refactor = new RefactorConvert(log, fs, baseDir, null, verbose);
			refactor.convert(filter, new Regex(""), regexs, excludeStrings, excludeComments);
		}
		else
		{
			Lib.println("Recursive find and replace in files using rules files.");
			Lib.println("Usage: haxelib run refactor [-v] process [ --exclude-string-literals ] [ --exclude-comments ] <baseDir> <filter> <rulesFile1> [ ...  <rulesFileN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    --exclude-string-literals   Exclude C-like strings from search.");
			Lib.println("    --exclude-comments          Exclude C-like comments from search.");
			Lib.println("    <baseDir>                   Path to folder to start search for files.");
			Lib.println("    <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <rulesFile>                 Path to rules file which contains one rule per line:");
			Lib.println("                                VAR = regexp");
			Lib.println("                                or");
			Lib.println("                                /search_can_contain_VAR/replacement/flags");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor process src *.hx beauty_haxe.rules");
			Lib.println("        Search for *.hx files in the 'src' folder.");
			Lib.println("        Read rules from file 'beauty_haxe.rules'. Rules example:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*");
			Lib.println("            ARGS = (?:\\s*ID\\s*(?:,\\s*ID\\s*)*)?");
			Lib.println("            SPACE = [ \\t\\r\\n]");
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
			options.add("postRulesFile", "");
			
			options.parse(args);
			
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var outDir = options.get("outDir");
			var rulesFile = options.get("rulesFile");
			var postRulesFile = options.get("postRulesFile");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (outDir == "") fail("<outDir> arg must be specified.");
			if (rulesFile == "") fail("<rulesFile> arg must be specified.");
			
			var refactor = new RefactorExtract(log, fs, baseDir, outDir, verbose);
			refactor.extract
			(
				filter,
				Rules.fromFile(getRulesFilePath(exeDir, rulesFile), verbose, log).regexs,
				postRulesFile != "" ? Rules.fromFile(getRulesFilePath(exeDir, postRulesFile), verbose, log).regexs : null
			);
		}
		else
		{
			Lib.println("Recursive find files and extract parts of them to separate files.");
			Lib.println("For example, you can split file contains many classes to separate class files.");
			Lib.println("Usage: haxelib run refactor [-v] extract <baseDir> <filter> <outDir> <extractRulesFile> [ <postRulesFile> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("    <baseDir>                   Path to source folder.");
			Lib.println("    <filter>                    File path's filter (regex or '*.ext;*.ext').");
			Lib.println("    <outDir>                    Output directory.");
			Lib.println("    <extractRulesFile>          Path to rules file (see 'convert' command).");
			Lib.println("                                Each rule must match begin of the extracted text and return a new file name.");
			Lib.println("                                For example: \"/class (\\w+) \\{/$1.hx\".");
			Lib.println("                                If matched text ends by open bracket '(', '[' or '{'");
			Lib.println("                                when extracted text will be extended to matched close bracket.");
			Lib.println("    <postRulesFile>             Rules to postprocess generated files.");
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor extract src *.hx out split_haxe.rules");
			Lib.println("        This command extract classes to separate files.");
			Lib.println("        Content of the 'split_haxe.rules' file:");
			Lib.println("            ID = [_a-zA-Z][_a-zA-Z0-9]*");
			Lib.println("            SPACE = [ \\t\\n]");
			Lib.println("            /class(?:SPACE+)(ID)(?:SPACE*){/$1.hx");
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