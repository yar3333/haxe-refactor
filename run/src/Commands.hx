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
		var options = new CmdOptions();
		
		options.add("excludeStrings", false, [ "-es", "--exclude-string-literals" ], "Exclude C-like strings from search.");
		options.add("excludeComments", false, [ "-ec", "--exclude-comments" ], "Exclude C-like comments from search.");
		options.add("baseDirs", "", "Paths to base folders. Use ';' as delimiter.\nUse '*' to specify 'any folder' in path.");
		options.add("filter", "", "File path's filter (regex or '*.ext;*.ext').");
		options.addRepeatable("regex", String, "Regex to find and replace (/search/replacement/flags).\nIn <replacement> use $1-$9 to substitute groups.\nUse '^' and 'v' between '$' and number to make uppercase/lowercase (like '$^1').");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var excludeStrings = options.get("excludeStrings");
			var excludeComments = options.get("excludeComments");
			var baseDirs = options.get("baseDirs");
			var filter = filterToRegex(filterToRegex(options.get("filter")));
			var regexs : Array<String> = options.get("regex");
			
			if (baseDirs == "") fail("<baseDirs> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (regexs.length == 0) fail("<regex> arg must be specified.");
			
			var refactor = new RefactorReplace(log, fs, baseDirs, null, verbose);
			var rules = Rules.fromLines(regexs, verbose, log);
			if (rules.check())
			{
				refactor.replaceInFiles(new EReg(filter, "i"), new Regex(""), rules.regexs, excludeStrings, excludeComments);
			}
		}
		else
		{
			Lib.println("Recursive find and replace in files.");
			Lib.println("Usage: haxelib run refactor [-v] replace [ -es ] [ -ec ] <baseDirs> <filter> <regex1> [ ... <regexN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor replace src *.hx /abc/def/");
			Lib.println("        Files will be recursively found in 'src' folder.");
			Lib.println("        Only haxe code files will be processed.");
			Lib.println("        String 'abc' will be replaced to 'def'.");
			Lib.println("");
			Lib.println("    haxelib run refactor replace */src;*/library *.hx;*.xml /(.)bc/$^1ef/");
			Lib.println("        Files will be recursively found in 'anydir/src' and 'anydir/library' folders.");
			Lib.println("        Haxe and xml files will be processed.");
			Lib.println("        Next strings will be replaced:");
			Lib.println("            abc => Aef");
			Lib.println("            .bc => .ef");
			Lib.println("            ...");
			
		}
	}
	
	public function replaceInFile(args:Array<String>)
	{
		var options = new CmdOptions();
		
		options.add("excludeStrings", false, [ "-es", "--exclude-string-literals" ], "Exclude C-like strings from search.");
		options.add("excludeComments", false, [ "-ec", "--exclude-comments" ], "Exclude C-like comments from search.");
		options.add("filePath", "", "Path to file.");
		options.addRepeatable("regex", String, "Regex to find and replace (/search/replacement/flags).");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var excludeStrings = options.get("excludeStrings");
			var excludeComments = options.get("excludeComments");
			var filePath = options.get("filePath");
			var regexs = options.get("regex");
			
			var refactor = new RefactorReplace(log, fs, null, null, verbose);
			var rules = Rules.fromLines(regexs, verbose, log);
			if (rules.check())
			{
				refactor.replaceInFile(filePath, rules.regexs, filePath, excludeStrings, excludeComments);
			}
		}
		else
		{
			Lib.println("Find and replace in file.");
			Lib.println("Usage: haxelib run refactor [-v] replaceInFile [ -es ] [ -ec ] <filePath> <regex1> [ ... <regexN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
			Lib.println("");
			Lib.println("Examples:");
			Lib.println("");
			Lib.println("    haxelib run refactor replaceInFile src/MyClass.hx /abc/def/i");
		}
	}
	
	public function rename(args:Array<String>)
	{
		var options = new CmdOptions();
		
		options.add("baseDir", "", "Path to source folder.");
		options.add("src", "", "Source package or full class name.\nCan be specified in disk path form.");
		options.add("dest", "", "Destination package or full class name.\nCan be specified in disk path form.");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var baseDir = options.get("baseDir");
			var src : String = options.get("src");
			var dest = options.get("dest");
			
			src = DirTools.pathToPack(baseDir, src, log, verbose);
			if (src == null) fail("<src> specified in disk path form, but do not starts with one of base dirs.");
			
			dest = DirTools.pathToPack(baseDir, dest, log, verbose);
			if (dest == null) fail("<dest> specified in disk path form, but do not starts with one of base dirs.");
			
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
		{
			Lib.println("Rename package or class.");
			Lib.println("Usage: haxelib run refactor [-v] rename <baseDir> <src> <dest>");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
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
			Lib.println("");
			Lib.println("    haxelib run refactor rename src src/mypackA/MyClass1.hx src/mypackB/MyClass2.hx");
			Lib.println("        Example of using path form.");
		}
	}
	
	public function convert(args:Array<String>)
	{
		var options = new CmdOptions();
		
		options.add("excludeStrings", false, [ "-es", "--exclude-string-literals" ], "Exclude C-like strings from search.");
		options.add("excludeComments", false, [ "-ec", "--exclude-comments" ], "Exclude C-like comments from search.");
		options.add("baseDir", "", "Path to source folder.");
		options.add("filter", "", "File path's filter (regex or '*.ext;*.ext').");
		options.add("outDir", "", "Output directory.");
		options.add("convertFileName", "", "Regex to find and replace in file name (/search/replacement/flags).\nUsed to produce output file name.");
		options.addRepeatable("rulesFile", String, "Path to rules file which contains one rule per line:\nVAR = regexp\nor\n/search_can_contain_VAR/replacement/flags");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var excludeStrings = options.get("excludeStrings");
			var excludeComments = options.get("excludeComments");
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var outDir = options.get("outDir");
			var convertFileName = new Regex(options.get("convertFileName"));
			var rulesFile : Array<String> = options.get("rulesFile");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (outDir == "") fail("<outDir> arg must be specified.");
			if (convertFileName == null) fail("<convertFileName> arg must be specified.");
			if (rulesFile.length == 0) fail("<rulesFile> arg must be specified.");
			
			var regexs = [];
			for (file in rulesFile)
			{
				regexs = regexs.concat(Rules.fromFile(getRulesFilePath(exeDir, file), verbose, log).regexs);
			}
			var refactor = new RefactorConvert(log, fs, baseDir, outDir, verbose);
			refactor.convert(filter, convertFileName, regexs, excludeStrings, excludeComments);
		}
		else
		{
			Lib.println("Recursive find and replace in files using rule files.");
			Lib.println("Usage: haxelib run refactor [-v] convert [ -es ] [ -ec ] <baseDir> <filter> <outDir> <convertFileName> <rulesFile1> [ ... <rulesFileN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
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
		var options = new CmdOptions();
		
		options.add("excludeStrings", false, [ "-es", "--exclude-string-literals" ], "Exclude C-like strings from search.");
		options.add("excludeComments", false, [ "-ec", "--exclude-comments" ], "Exclude C-like comments from search.");
		options.add("baseDir", "", "Path to folder to start search for files.");
		options.add("filter", "", "File path's filter (regex or '*.ext;*.ext').");
		options.addRepeatable("rulesFile", String, "Path to rules file which contains one rule per line:\nVAR = regexp\nor\n/search_can_contain_VAR/replacement/flags");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var excludeStrings = options.get("excludeStrings");
			var excludeComments = options.get("excludeComments");
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var rulesFile : Array<String> = options.get("rulesFile");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (rulesFile.length == 0) fail("<rulesFile> arg must be specified.");
			
			var regexs = [];
			for (file in rulesFile)
			{
				regexs = regexs.concat(Rules.fromFile(getRulesFilePath(exeDir, file), verbose, log).regexs);
			}
			var refactor = new RefactorConvert(log, fs, baseDir, null, verbose);
			refactor.convert(filter, new Regex(""), regexs, excludeStrings, excludeComments);
		}
		else
		{
			Lib.println("Recursive find and replace in files using rules files.");
			Lib.println("Usage: haxelib run refactor [-v] process [ -es ] [ -ec ] <baseDir> <filter> <rulesFile1> [ ...  <rulesFileN> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
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
		var options = new CmdOptions();
		
		options.add("baseDir", "", "Path to source folder.");
		options.add("filter", "", "File path's filter (regex or '*.ext;*.ext').");
		options.add("outDir", "", "Output directory.");
		options.add("extractRulesFile", "", "Path to rules file (see 'convert' command).\nEach rule must match begin of the extracted text and return a new file name.\nFor example: \"/class (\\w+) \\{/$1.hx\".\nIf matched text ends by open bracket '(', '[' or '{'\nwhen extracted text will be extended to matched close bracket.");
		options.add("postRulesFile", "", "Rules to postprocess generated files.");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var baseDir = options.get("baseDir");
			var filter = filterToRegex(options.get("filter"));
			var outDir = options.get("outDir");
			var extractRulesFile = options.get("extractRulesFile");
			var postRulesFile = options.get("postRulesFile");
			
			if (baseDir == "") fail("<baseDir> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (outDir == "") fail("<outDir> arg must be specified.");
			if (extractRulesFile == "") fail("<extractRulesFile> arg must be specified.");
			
			var refactor = new RefactorExtract(log, fs, baseDir, outDir, verbose);
			refactor.extract
			(
				filter,
				Rules.fromFile(getRulesFilePath(exeDir, extractRulesFile), verbose, log).regexs,
				postRulesFile != "" ? Rules.fromFile(getRulesFilePath(exeDir, postRulesFile), verbose, log).regexs : null
			);
		}
		else
		{
			Lib.println("Recursive find files and extract parts of them to separate files.");
			Lib.println("For example, you can split file contains many classes to separate class files.");
			Lib.println("Usage: haxelib run refactor [-v] extract <baseDir> <filter> <outDir> <extractRulesFile> [ <postRulesFile> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
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
		var options = new CmdOptions();
		
		options.add("srcDirs", "", "Paths to source folders. Use ';' as delimiter.\nUse '*' to specify 'any folder' in path.");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var srcDirs = options.get("srcDirs");
			
			if (srcDirs == "") fail("<srcDirs> arg must be specified.");
			
			var refactor = new RefactorOverride(log, fs, srcDirs, null, verbose);
			refactor.overrideInFiles();
		}
		else
		{
			Lib.println("Autofix override/overload/redefinition in haxe extern class members.");
			Lib.println("Usage: haxelib run refactor [-v] override <srcDirs>");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
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
	}
	
	public function reindent(args:Array<String>)
	{
		var options = new CmdOptions();
		
		options.add("baseDirs", "", "Paths to folders (like 'mydirA;mydirB').");
		options.add("filter", "", "File path's filter (regex or '*.ext;*.ext').");
		options.add("oldTabSize", -1, "Spaces per tab in old style.");
		options.add("oldIndentSize", -1, "Spaces per indent in old style.");
		options.add("newTabSize", -1, "Spaces per tab in new style.");
		options.add("newIndentSize", -1, "Spaces per indent in new style.");
		options.add("shiftSize", 0, "Shift to left(-) or right(+) to specified spaces.");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var baseDirs = options.get("baseDirs");
			var filter = filterToRegex(options.get("filter"));
			var oldTabSize = options.get("oldTabSize");
			var oldIndentSize = options.get("oldIndentSize");
			var newTabSize = options.get("newTabSize");
			var newIndentSize = options.get("shiftSize");
			var shiftSize = options.get("shiftSize");
			
			if (baseDirs == "") fail("<baseDirs> arg must be specified.");
			if (filter == "") fail("<filter> arg must be specified.");
			if (oldTabSize == -1) fail("<oldTabSize> arg must be specified.");
			if (oldIndentSize == -1) fail("<oldIndentSize> arg must be specified.");
			if (newTabSize == -1) fail("<newTabSize> arg must be specified.");
			if (newIndentSize == -1) fail("<newIndentSize> arg must be specified.");
			
			var refactor = new RefactorReindent(log, fs, baseDirs, null, verbose);
			refactor.reindent(new EReg(filter, "i"), oldTabSize, oldIndentSize, newTabSize, newIndentSize, shiftSize);
		}
		else
		{
			Lib.println("Change indentation in the files.");
			Lib.println("Usage: haxelib run refactor [-v] reindent <baseDirs> <filter> <oldTabSize> <oldIndentSize> <newTabSize> <newIndentSize> [ <shiftSize> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
			Lib.println("");
			Lib.println("Example:");
			Lib.println("");
			Lib.println("    haxelib run refactor reindent src *.hx 4 2 4 4 4");
		}
	}
	
	public function reindentInFile(args:Array<String>)
	{
		var options = new CmdOptions();
		
		options.add("filePath", "", "Path to file.");
		options.add("oldTabSize", -1, "Spaces per tab in old style.");
		options.add("oldIndentSize", -1, "Spaces per indent in old style.");
		options.add("newTabSize", -1, "Spaces per tab in new style.");
		options.add("newIndentSize", -1, "Spaces per indent in new style.");
		options.add("shiftSize", 0, "Shift to left(-) or right(+) to specified spaces.");
		
		if (args.length > 0)
		{
			options.parse(args);
			
			var filePath = options.get("filePath");
			var oldTabSize = options.get("oldTabSize");
			var oldIndentSize = options.get("oldIndentSize");
			var newTabSize = options.get("newTabSize");
			var newIndentSize = options.get("shiftSize");
			var shiftSize = options.get("shiftSize");
			
			if (filePath == "") fail("<filePath> arg must be specified.");
			if (oldTabSize == -1) fail("<oldTabSize> arg must be specified.");
			if (oldIndentSize == -1) fail("<oldIndentSize> arg must be specified.");
			if (newTabSize == -1) fail("<newTabSize> arg must be specified.");
			if (newIndentSize == -1) fail("<newIndentSize> arg must be specified.");
			
			var refactor = new RefactorReindent(log, fs, null, null, verbose);
			refactor.reindentFile(filePath, oldTabSize, oldIndentSize, newTabSize, newIndentSize, shiftSize);
		}
		else
		{
			Lib.println("Change indentation in the file.");
			Lib.println("Usage: haxelib run refactor [-v] reindentInFile <filePath> <oldTabSize> <oldIndentSize> <newTabSize> <newIndentSize> [ <shiftSize> ]");
			Lib.println("where '-v' is the verbose key. Command args description:");
			Lib.println("");
			Lib.print(options.getHelpMessage());
			Lib.println("");
			Lib.println("Example:");
			Lib.println("");
			Lib.println("    haxelib run refactor reindentInFile MyClass.hx 4 2 4 4 4");
		}
	}
}