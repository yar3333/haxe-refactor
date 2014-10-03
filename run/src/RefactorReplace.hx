import haxe.io.Path;
import stdlib.Regex;
using StringTools;

class RefactorReplace extends Refactor
{
	public function replaceInFiles(filter:EReg, changeFileName:Regex, rules:Array<Regex>, excludeStrings:Bool, excludeComments:Bool)
	{
		for (baseDir in baseDirs)
		{
			log.start("Replace in '" + baseDir + "'");
			
			fs.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (filter.match(localPath))
				{
					if (outDir == null)
					{
						replaceInFile(path, rules, Path.directory(path) + "/" + changeFileName.replace(Path.withoutDirectory(path)), excludeStrings, excludeComments);
					}
					else
					{
						var localDir = Path.directory(localPath);
						replaceInFile(path, rules, outDir + (localDir != "" ? Path.addTrailingSlash(localDir) : "") + changeFileName.replace(Path.withoutDirectory(localPath)), excludeStrings, excludeComments);
					}
				}
			});
			
			log.finishOk();
		}
	}
	
	public function replaceInFile(inpPath:String, rules:Array<Regex>, outPath:String, excludeStrings:Bool, excludeComments:Bool)
	{
		if (verbose) log.start("Search in '" + inpPath + "'");
		
		new TextFile(fs, inpPath, outPath, verbose, log).process(function(text, _)
		{
			if (!excludeStrings && !excludeComments)
			{
				for (rule in rules)
				{
					text = rule.replace(text, verbose ? function(s) log.trace(s) : null);
				}
			}
			else
			{
				for (rule in rules)
				{
					var r = "";
					
					var reStr = (excludeStrings ? "(\"|')(?:\\\\.|.)*?\\1" : "({9a5a7986-d5e5-4c5e-92fc-ee557254d67f})")
							  + "|"
							  + (excludeComments ? "(/\\*.*?\\*/|^//.*?$)" : "({9a5a7986-d5e5-4c5e-92fc-ee557254d67f})");
					var re = new EReg(reStr, "m");
					var i = 0; while (re.matchSub(text, i))
					{
						var p = re.matchedPos();
						
						if (excludeStrings && re.matched(1) != null)
						{
							r += rule.replace(text.substr(i, p.pos - i + 1), verbose ? function(s) log.trace(s) : null);
							r += re.matched(0).substr(1, p.len - 2);
							i = p.pos + p.len - 1;
						}
						else
						{
							r += rule.replace(text.substr(i, p.pos - i), verbose ? function(s) log.trace(s) : null);
							r += re.matched(0);
							i = p.pos + p.len;
						}
					}
					r += rule.replace(text.substr(i), verbose ? function(s) log.trace(s) : null);
					text = r;
				}
			}
			return text;
		});
		
		if (verbose) log.finishOk();
	}
}