import hant.Log;
import stdlib.Regex;
import sys.io.File;
using StringTools;

class Rules
{
	public var regexs(default, null) : Array<Regex>;
	
	public function new(regexs:Array<Regex>)
	{
		this.regexs = regexs;
	}
	
	public static function fromFile(rulesFile:String) : Rules
	{
		return fromText(File.getContent(rulesFile));
	}
	
	public static function fromLines(ruleLines:Array<String>) : Rules
	{
		return fromText(ruleLines.join("\n"));
	}
	
	public static function fromText(text:String) : Rules
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
		
		return new Rules(regexs);
	}
	
	public function check() : Bool
	{
		Log.start("Check rules");
		for (regex in regexs)
		{
			Log.start(regex.search + " => " + regex.replacement);
			try
			{
				new EReg(regex.search, "g");
				Log.finishSuccess();
			}
			catch (e:Dynamic)
			{
				Log.finishFail(Std.string(e));
				return false;
			}
		}
		Log.finishSuccess();
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