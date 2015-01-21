import hant.Log;
import stdlib.Regex;
import sys.io.File;
using StringTools;

class Rules
{
	var verbose : Bool;
	
	public var regexs(default, null) : Array<Regex>;
	
	public function new(regexs:Array<Regex>, verbose:Bool)
	{
		this.regexs = regexs;
		this.verbose = verbose;
	}
	
	public static function fromFile(rulesFile:String, verbose:Bool) : Rules
	{
		return fromText(File.getContent(rulesFile), verbose);
	}
	
	public static function fromLines(ruleLines:Array<String>, verbose:Bool) : Rules
	{
		return fromText(ruleLines.join("\n"), verbose);
	}
	
	public static function fromText(text:String, verbose:Bool) : Rules
	{
		var regexs = [];
		
		var lines = text.replace("\r", "").split("\n");
		var consts = new Map<String, String>();
		for (line in lines)
		{
			line = line.trim();
			
			if (line == "" || line.startsWith("//")) continue;
			
			var reConst = ~/^([_a-zA-Z][_a-zA-Z0-9]*)\s*[=]\s*(.+?)$/;
			
			if (reConst.match(line))
			{
				var value = reConst.matched(2);
				for (constName in consts.keys())
				{
					value = replaceWord(value, constName, consts.get(constName));
				}
				consts.set(reConst.matched(1), value);
			}
			else
			{
				for (constName in consts.keys())
				{
					line = replaceWord(line, constName, consts.get(constName));
				}
				regexs.push(new Regex(line.replace("\t", "")));
			}
		}
		
		return new Rules(regexs, verbose);
	}
	
	public function check() : Bool
	{
		if (verbose) Log.start("Check rules");
		for (regex in regexs)
		{
			if (verbose) Log.start(regex.search + " => " + regex.replacement);
			try
			{
				new EReg(regex.search, "g");
				if (verbose) Log.finishSuccess();
			}
			catch (e:Dynamic)
			{
				if (verbose) Log.finishFail();
				Log.echo(e);
				return false;
			}
		}
		if (verbose) Log.finishSuccess();
		return true;
	}
	
	static function replaceWord(src:String, search:String, replacement:String) : String
	{
		return new EReg("(^|[^_a-zA-Z0-9])" + search + "($|[^_a-zA-Z0-9])", "g").map(src, function(re)
		{
			return re.matched(1) + replacement + re.matched(2);
		});
	}
}