import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import stdlib.Regex;
import sys.io.File;
using StringTools;

class RegexProcessor
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
			line = line.trim();
			
			if (line == "" || line.startsWith("//")) continue;
			
			var reConst = ~/^([_a-zA-Z][_a-zA-Z0-9]*)\s*[=]\s*(.+?)$/;
			
			if (reConst.match(line))
			{
				var value = reConst.matched(2);
				for (const in consts)
				{
					value = replaceWord(value, const.name, const.value);
				}
				consts.push({ name:reConst.matched(1), value:value });
			}
			else
			{
				for (const in consts)
				{
					line = replaceWord(line, const.name, const.value);
				}
				rules.push(new Regex(line));
			}
		}
	}
	
	public function convert(baseDir:String, filter:String, outDir:String, changeFileName:Regex, excludeStrings:Bool, excludeComments:Bool)
	{
		var refactor = new Refactor(log, fs, baseDir, outDir, verbose);
		if (refactor.checkRules(rules))
		{
			refactor.replaceInFiles(new EReg(filter, "i"), changeFileName, rules, excludeStrings, excludeComments);
		}
	}
	
	public function extract(baseDir:String, filter:String, outDir:String)
	{
		var refactor = new Refactor(log, fs, baseDir, outDir, verbose);
		if (refactor.checkRules(rules))
		{
			var reFilter = new EReg(filter, "i");
			
			fs.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (reFilter.match(localPath))
				{
					var localDir = Path.directory(localPath);
					extractFromFile(path, rules, outDir + (localDir != "" ? "/" + localDir : ""));
				}
			});
		}
	}
	
	function extractFromFile(inpPath:String, rules:Array<Regex>, outDir:String)
	{
		if (verbose) log.start("Extract from '" + inpPath + "'");
		
		new TextFile(fs, inpPath, verbose, log).process(function(text)
		{
			for (rule in rules)
			{
				for (match in rule.matchAll(text))
				{
					var destPath = outDir + "/" + match.replacement;
					var pos = match.pos + match.len;
					var begText = text.substr(match.pos, match.len);
					var endText = "([{".indexOf(begText.substr(-1)) >= 0 
						? text.substr(pos, findCloseBracketIndex(text, pos - 1) - pos)
						: "";
					
					log.start("Save file " + destPath);
					TextFile.save(fs, destPath, begText + endText);
					log.finishOk();
				}
			}
			return null;
		});
		
		if (verbose) log.finishOk();
	}
	
	static function findCloseBracketIndex(text:String, openBacketIndex:Int)
	{
		var stack = "";
		var i = openBacketIndex; while (i < text.length)
		{
			var c = text.charAt(i);
			
			if (c == "/" && i + 1 < text.length && text.charAt(i + 1) == "*")
			{
				i = text.indexOf("*/", i + 2) + 1;
			}
			else
			{
				if (c == "(") stack += ")";
				else
				if (c == "[") stack += "]";
				else
				if (c == "{") stack += "}";
				else
				if ("}])".indexOf(c) >= 0 && stack.substr( -1) == c) stack = stack.substr(0, stack.length - 1);
			}
			
			i++;
			
			if (stack.length == 0) return i;
		}
		return openBacketIndex;
	}
	
	static function replaceWord(src:String, search:String, replacement:String) : String
	{
		return new EReg("(^|[^_a-zA-Z0-9])" + search + "($|[^_a-zA-Z0-9])", "g").map(src, function(re)
		{
			return re.matched(1) + replacement + re.matched(2);
		});
	}
}