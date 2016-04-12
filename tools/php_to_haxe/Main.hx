import hant.CmdOptions;
import hant.Log;
import haxe.io.Path;
import php.JsonNatives;
import php.Lib;
import stdlib.Exception;
import sys.FileSystem;
import sys.io.File;
import stdlib.Std;
import php.Tokens;
import tjson.TJSON;
using stdlib.StringTools;
using stdlib.Lambda;

class Main
{
    static function main()
    {
		var args = Sys.args();
		
		if (args.length < 3 || args.length > 4)
		{
			Log.echo("Usage: php_to_haxe [ --config <pathToConfigFile.json> ] code|extern <in_file.php> [<out_file.hx>]");
		}
		else
		{
			var options = new CmdOptions();
			options.add("configFile", "php_to_haxe.json", ["--config"], "Path to config file. Default is 'php_to_haxe.json'.");
			options.add("mode", "");
			options.add("from", "");
			options.add("to", "");
			options.parse(args);
			
			var configFile = options.get("configFile");
			var config = FileSystem.exists(configFile)
					   ? loadConfig(configFile)
					   : (Path.directory(configFile) == "" ? loadConfig(Path.directory(Sys.executablePath()) + "/" + configFile) : null);
			
			if (config == null) { Sys.println("Config file '" + configFile + "' not found."); return 1; }
			
			var mode = options.get("mode");
			var from = options.get("from");
			var to = options.get("to");
			
			if (to == "")
			{
				to = Path.join([ Path.directory(from), Path.withoutDirectory(Path.withoutExtension(from)).capitalize() + ".hx"  ]);
			}
			
			Log.start(from + " => " + to);
			
			try
			{
				var phpToHaxe = new PhpToHaxe
				(
					config.typeNamesMapping,
					config.varNamesMapping,
					config.functionNameMapping,
					config.magickFunctionNameMapping,
					config.reservedWords,
					mode == "extern"
				);
				if (!FileSystem.exists(from)) throw "Input file not exists.";
				var inp = File.getContent(from);
				var out = phpToHaxe.getHaxeCode(inp);
				File.saveContent(to, out);
				
				Log.finishSuccess();
			}
			catch (e:Dynamic)
			{
				trace(Exception.string(e));
			}
		}
		
		return 0;
	}
	
	static function loadConfig(path:String) : { typeNamesMapping:Dynamic, varNamesMapping:Dynamic, functionNameMapping:Dynamic, magickFunctionNameMapping:Dynamic, reservedWords:Dynamic }
	{
		if (!FileSystem.exists(path))
		{
			trace("Not found: " + path);
			return null;
		}
		
		var r = TJSON.parse(File.getContent(path));
		r.typeNamesMapping = Std.hash(r.typeNamesMapping);
		r.varNamesMapping = Std.hash(r.varNamesMapping);
		r.functionNameMapping = Std.hash(r.functionNameMapping);
		r.magickFunctionNameMapping = Std.hash(r.magickFunctionNameMapping);
		r.reservedWords = r.reservedWords;
		
		return r;
	}
	
	
}
