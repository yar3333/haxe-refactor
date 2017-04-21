import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import stdlib.Regex;
import sys.FileSystem;
import sys.io.File;

class RefactorExtract extends RefactorReplace
{
	public function extract(filter:String, regexs:Array<Regex>, ?postRegexs:Array<Regex>, append:Bool, saveNotExtracted:String, baseLogLevel:Int)
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
					extractFromFile(path, regexs, Path.removeTrailingSlashes(outDir) + (localDir != "" ? "/" + localDir : ""), postRegexs, append, saveNotExtracted, baseLogLevel);
				}
			});
		}
	}
	
	function extractFromFile(inpPath:String, regexs:Array<Regex>, outDir:String, ?postRegexs:Array<Regex>, append:Bool, saveNotExtracted:String, baseLogLevel:Int)
	{
		Log.start("Extract from '" + inpPath, baseLogLevel);
		
		var file = new TextFile(inpPath, null, baseLogLevel + 1);
		file.process(function(text, fileApi)
		{
			for (regex in regexs)
			{
				var blocks = new Array<{ beg:Int, end:Int }>();
				
				for (match in regex.matchAll(text))
				{
					var destPath = outDir + "/" + match.replacement;
					var endPos = "([{".indexOf(text.charAt(match.pos + match.len - 1)) >= 0 
						? findCloseBracketIndex(text, match.pos + match.len - 1)
						: match.pos + match.len;
					
					blocks.push({ beg:match.pos, end:endPos });
					
					var s = text.substring(match.pos, endPos);
					
					if (postRegexs != null)
					{
						s = new RefactorReplace(null, null).replaceInText(s, postRegexs, true, true, baseLogLevel + 2);
					}
					
					if (fileApi.save(destPath, (append && FileSystem.exists(destPath) ? File.getContent(destPath) + "\n" : "") + s))
					{
						Log.echo(destPath, baseLogLevel + 2);
					}
				}
				
				blocks.reverse();
				for (block in blocks)
				{
					text = text.substring(0, block.beg) + text.substring(block.end);
				}
			}
			
			if (saveNotExtracted != null && saveNotExtracted != "")
			{
				File.saveContent((append && FileSystem.exists(saveNotExtracted) ? File.getContent(saveNotExtracted) + "\n" : "") + saveNotExtracted, text);
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