import hant.Log;
import hant.Path;
import neko.Lib;
import sys.FileSystem;
using StringTools;

class Main 
{
	static function main() : Void
	{
        var args = Sys.args();

        var exeDir: String;
        if (Sys.programPath().endsWith(".exe"))
        {
            exeDir = Path.normalize(Path.directory(Sys.programPath()));
        }
        else
        {
		    exeDir = Path.normalize(Sys.getCwd());
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
        }
        
		var verboseLevel = 0;
		for (i in 0...args.length)
		{
			if (~/^-v+$/.match(args[i]))
			{
				verboseLevel = args[i].length - 1;
				args.splice(i, 1);
				break;
			}
		}
		Log.instance = new Log(999, verboseLevel);
		
		if (args.length > 0)
		{
			var commands = new Commands(exeDir);
			
			var command = args.shift();
			switch (command)
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
				case "extractFromFile":	commands.extractFromFile(args);
				case "override":		commands.doOverride(args);
				case "overloadInFile":	commands.overloadInFile(args);
				case "overloadInText":	commands.overloadInText(args);
				case "reindent":		commands.reindent(args);
				case "reindentFile":	commands.reindentFile(args);
				case "reindentText":	commands.reindentText(args);
				case "renameFiles":		commands.renameFiles(args, verboseLevel > 0);
				case "lineEndings":		commands.lineEndings(args);
				case "fixPackage":		commands.fixPackage(args);
				case "fixImports":		commands.fixImports(args);
				default:
					var script = exeDir + "/scripts/" + command + (Sys.systemName() == "Windows" ? ".cmd" : "");
					if (FileSystem.exists(script))
					{
						
						Sys.exit(Sys.command(script, args));
						return;
					}
					fail("Unknow command '" + command + "'.");
			}
		}
		else
		{
			summaryHelp(exeDir);
		}
	}
	
	static function fail(message:String)
	{
		Lib.println("ERROR: " + message);
		Sys.exit(1);
	}
	
	static function summaryHelp(exeDir:String)
	{
		Lib.println("Refactor is a refactoring and search/replace tool.");
		Lib.println("Usage: haxelib run refactor [-v] (<command> | <script>) <args>");
		Lib.println("where '-v' is the verbose key ('-vv' for more details).");
		Lib.println("");
		Lib.println("Commands:");
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
		Lib.println("    extractFromFile Save parts of specified text file into separate files.");
		Lib.println("    override        Autofix override/overload/redefinition in haxe code.");
		Lib.println("    overloadInFile  Autofix overload/redefinition in haxe code.");
		Lib.println("    overloadInText  Line overloadInFile, but read from stdin and write to stdout.");
		Lib.println("    reindent        Recursive change indentation in files.");
		Lib.println("    reindentFile    Change indentation in specified file.");
		Lib.println("    reindentText    Like reindentFile, but read from stdin and write to stdout.");
		Lib.println("    renameFiles     Rename files recursively by regex.");
		Lib.println("    lineEndings     Recursive fix line endings in files.");
		Lib.println("    fixPackage      Fix package in haxe files from relative directory path.");
		Lib.println("    fixImports      Fix imports by running haxe compiler and read errors.");
		Lib.println("");
		
		var isWindows = Sys.systemName() == "Windows";
		var scriptsDir = exeDir + "/scripts";
		Lib.println("Scripts:");
		for (file in FileSystem.readDirectory(scriptsDir))
		{
			if (isWindows && Path.extension(file) == "cmd" || !isWindows && Path.extension(file) == "")
			{
				Lib.println("    " + Path.withoutExtension(file));
			}
		}
		
		Lib.println("");
		Lib.println("Type 'haxelib run refactor <command>' to get help about specified command.");
	}
}
