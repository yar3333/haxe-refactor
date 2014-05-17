import hant.FileSystemTools;
import hant.Log;
import stdlib.Exception;
import stdlib.Regex;
import sys.io.File;
using StringTools;

class Convert
{
	var log : Log;
	var fs : FileSystemTools;
	var verbose : Bool;
	
	var rules : Array<Regex>;

	public function new(log:Log, fs:FileSystemTools, verbose:Bool, rulesFile:String) 
	{
		this.log = log;
		this.fs = fs;
		this.verbose = verbose;
		
		rules = [];
		
		var lines = File.getContent(rulesFile).replace("\r", "").split("\n");
		var consts = new Array<{ name:String, value:String }>();
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
						value = replaceWord(value, const.name, const.value);
					}
					consts.push({ name:name, value:value });
				}
				else
				{
					if (line.trim().length > 0)
					{
						throw new Exception("Error in line '" + line + "'.");
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
						line = replaceWord(line, const.name, const.value);
					}
					rules.push(new Regex(line));
				}
			}
		}
	}
	
	public function process(baseDir:String, filter:String, outDir:String, changeFileName:Regex, excludeStrings:Bool)
	{
		var refactor = new Refactor(log, fs, baseDir, outDir, verbose);
		if (refactor.checkRules(rules))
		{
			refactor.replaceInFiles(new EReg(filter, "i"), changeFileName, rules, excludeStrings);
		}
	}
	
	static function replaceWord(src:String, search:String, replacement:String) : String
	{
		return new EReg("(^|[^_a-zA-Z0-9])" + search + "($|[^_a-zA-Z0-9])", "g").map(src, function(re)
		{
			return re.matched(1) + replacement + re.matched(2);
		});
	}
}