import hant.FileSystemTools;
import hant.Log;
import hant.Path;
import neko.Lib;

class Main 
{
	static function main() 
	{
        var args = Sys.args();
		
		var exeDir = Path.normalize(Sys.getCwd());
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
			var verboseLevel = 0;
			
			var arg = args.shift();
			if (~/^-v+$/.match(arg))
			{
				verboseLevel = arg.length - 1;
				arg = args.shift();
			}
			
			Log.instance = new Log(999, verboseLevel);
			
			var commands = new Commands(exeDir);
			
			switch (arg)
			{
				case "replace":			commands.replace(args);
				case "replaceInFile":	commands.replaceInFile(args, 1);
				case "replaceInText":	commands.replaceInText(args);
				case "rename":			commands.rename(args);
				case "convert":			commands.convert(args);
				case "convertFile":		commands.convertFile(args);
				case "process":			commands.process(args);
				case "processFile":		commands.processFile(args);
				case "processText":		commands.processText(args);
				case "extract":			commands.extract(args);
				case "override":		commands.doOverride(args);
				case "reindent":		commands.reindent(args);
				case "reindentInFile":	commands.reindentInFile(args);
				case "reindentFile":	commands.reindentInFile(args);
				case "reindentText":	commands.reindentText(args);
				default:
					fail("Unknow command.");
			}
		}
		else
		{
			summaryHelp();
		}
		
		Sys.exit(0);
	}
	
	static function fail(message:String)
	{
		Lib.println("ERROR: " + message);
		Sys.exit(1);
	}
	
	static function summaryHelp()
	{
		Lib.println("Refactor is a refactoring and search/replace tool.");
		Lib.println("Usage: haxelib run refactor [-v] <command> <args>");
		Lib.println("where '-v' is the verbose key and <command> may be:");
		Lib.println("");
		Lib.println("    replace         Recursive search&replace by regex in files.");
		Lib.println("    replaceInFile   Search&replace by regex in specified file.");
		Lib.println("    replaceInText   Like replaceInFile, but read from stdin and write to stdout.");
		Lib.println("    rename          Rename haxe package or class.");
		Lib.println("    convert         Massive apply regexes to files and save into other files.");
		Lib.println("    convertFile     Massive apply regexes to file and save into other file.");
		Lib.println("    process         Shortcut for \"convert\" for changing in-place.");
		Lib.println("    processFile     Shortcut for \"convertFile\" for changing in-place.");
		Lib.println("    processText     Like processFile, but read from stdin and write to stdout.");
		Lib.println("    extract         Search in files and save found texts into separate files.");
		Lib.println("    override        Autofix override/overload/redefinition in haxe code.");
		Lib.println("    reindent        Recursive change indentation in files.");
		Lib.println("    reindentInFile  Change indentation in specified file.");
		Lib.println("    reindentText    Like reindentInFile, but read from stdin and write to stdout.");
		Lib.println("");
		Lib.println("Type 'haxelib run refactor <command>' to get help about specified command.");
	}
}
