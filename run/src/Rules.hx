import hant.Log;
import stdlib.Regex;
import sys.io.File;
using StringTools;

class Rules
{
	var verbose : Bool;
	var log : Log;
	
	public var regexs(default, null) : Array<Regex>;
	
	public function new(verbose:Bool, log:Log, regexs:Array<Regex>)
	{
		this.log = log;
		this.verbose = verbose;
		this.regexs = regexs;
	}
	
	public static function fromFile(rulesFile:String, verbose:Bool, log:Log) : Rules
	{
		return fromText(File.getContent(rulesFile), verbose, log);
	}
	
	public static function fromLines(ruleLines:Array<String>, verbose:Bool, log:Log) : Rules
	{
		return fromText(ruleLines.join("\n"), verbose, log);
	}
	
	public static function fromText(text:String, verbose:Bool, log:Log) : Rules
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
		
		return new Rules(verbose, log, regexs);
	}
	
	public function check() : Bool
	{
		if (verbose) log.start("Check rules");
		for (regex in regexs)
		{
			if (verbose) log.start(regex.search + " => " + regex.replacement);
			try
			{
				new EReg(regex.search, "g");
				if (verbose) log.finishOk();
			}
			catch (e:Dynamic)
			{
				if (verbose) log.finishFail();
				log.trace(e);
				return false;
			}
		}
		if (verbose) log.finishOk();
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