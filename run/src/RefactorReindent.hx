import sys.FileSystem;
import sys.io.File;
using StringTools;

class RefactorReindent extends Refactor
{
	public function reindent(filter:EReg, oldTabSize:Int, oldIndentSize:Int, newTabSize:Int, newIndentSize:Int, shiftSize:Int)
	{
		for (baseDir in baseDirs)
		{
			log.start("Reindent in '" + baseDir + "'");
			
			fs.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (filter.match(localPath))
				{
					reindentFile(path, oldTabSize, oldIndentSize, newTabSize, newIndentSize, shiftSize);
				}
			});
			
			log.finishOk();
		}
	}
	
	public function reindentFile(path:String, oldTabSize:Int, oldIndentSize:Int, newTabSize:Int, newIndentSize:Int, shiftSize:Int) : Void
	{
		if (!FileSystem.exists(path) || FileSystem.isDirectory(path))
		{
			log.start("Reindent in '" + path + "'");
			log.finishFail("File not found.");
		}
		
		var oldText = File.getContent(path);
		oldText = oldText.replace("\r\n", "\n").replace("\r", "\n");
		
		var lines = oldText.split("\n");
		for (i in 0...lines.length)
		{
			var oldLine = lines[i];
			var newLine = "";
			var oldPos = 0;
			var j = 0; while (j < oldLine.length)
			{
				if      (oldLine.charAt(j) == " ")  oldPos++;
				else if (oldLine.charAt(j) == "\t") oldPos = (Std.int(oldPos / oldTabSize) + 1) * oldTabSize;
				else                                { newLine = oldLine.substr(j); break; }
				j++;
			}
			
			var oldIndents = Std.int(oldPos / oldIndentSize);
			var oldIndentAdditionalSpaces = oldPos % oldIndentSize;
			
			oldPos = oldIndents * newIndentSize + oldIndentAdditionalSpaces;
			
			var newPos = -shiftSize;
			var spaces = "";
			while (newTabSize > 0 && newPos + newTabSize <= oldPos) { newPos += newTabSize; spaces += "\t"; }
			while (newPos < oldPos) { newPos++; spaces += " "; }
			
			lines[i] = spaces + newLine;
		}
		
		var newText = lines.join("\n");
		if (newText != oldText)
		{
			File.saveContent(path, newText);
			if (verbose) log.trace("Fixed: " + path);
		}
	}
}