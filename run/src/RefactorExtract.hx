import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import stdlib.Regex;

class RefactorExtract extends RefactorReplace
{
	public function extract(filter:String, regexs:Array<Regex>, ?postRegexs:Array<Regex>)
	{
		for (baseDir in baseDirs)
		{
			var reFilter = new EReg(filter, "i");
			
			FileSystemTools.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (reFilter.match(localPath))
				{
					var localDir = Path.directory(localPath);
					extractFromFile(path, regexs, Path.removeTrailingSlashes(outDir) + (localDir != "" ? "/" + localDir : ""), postRegexs);
				}
			});
		}
	}
	
	function extractFromFile(inpPath:String, regexs:Array<Regex>, outDir:String, ?postRegexs:Array<Regex>)
	{
		Log.start("Extract from '" + inpPath);
		
		var file = new TextFile(inpPath, null, verboseLevel > 1);
		file.process(function(text, fileApi)
		{
			for (regex in regexs)
			{
				for (match in regex.matchAll(text))
				{
					var destPath = outDir + "/" + match.replacement;
					var pos = match.pos + match.len;
					var begText = text.substr(match.pos, match.len);
					var endText = "([{".indexOf(begText.substr(-1)) >= 0 
						? text.substr(pos, findCloseBracketIndex(text, pos - 1) - pos)
						: "";
						
					var text = begText + endText;
						
					if (postRegexs != null)
					{
						text = new RefactorReplace(null, null, verboseLevel).replaceInText(text, postRegexs, true, true, verboseLevel > 2);
					}
					
					if (fileApi.save(destPath, text))
					{
						Log.echo(destPath);
					}
				}
			}
			return null;
		});
		
		Log.finishSuccess();
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
}