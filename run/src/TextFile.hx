import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

typedef FileApi =
{
	function save(outPath:String, text:String) : Bool;
}

class TextFile
{
	var fs:FileSystemTools;
	var inpPath : String;
	var outPath : String;
	var verbose : Bool;
	var log : Log;
	
	var original : String;
	var isWinLineEndStyle : Bool;
	var isMacLineEndStyle : Bool;
	
	
	public function new(fs:FileSystemTools, inpPath:String, outPath:String, verbose:Bool, log:Log)
	{
		this.fs = fs;
		this.inpPath = inpPath;
		this.outPath = outPath;
		this.verbose = verbose;
		this.log = log;
	}
	
	public function process(f:String->FileApi->String)
	{
		var text = File.getContent(inpPath);
		
		original = text;
		
		isWinLineEndStyle = text.indexOf("\r\n") >= 0;
		if (isWinLineEndStyle) text = text.replace("\r\n", "\n");
		
		isMacLineEndStyle = !isWinLineEndStyle && text.indexOf("\r") >= 0;
		if (isMacLineEndStyle) text = text.replace("\r", "\n");
		
		text = f(text, cast this);
		
		if (text != null)
		{
			if (save(outPath, text))
			{
				if (verbose) log.trace("Fixed: " + outPath);
			}
		}
	}
	
	function save(outPath:String, text:String) : Bool
	{
		if (isMacLineEndStyle) text = text.replace("\n", "\r");
		else
		if (isWinLineEndStyle) text = text.replace("\n", "\r\n");
		
		if (inpPath == outPath && text == original) return false;
		
		var r = false;
		var isHidden = fs.getHiddenFileAttribute(outPath);
		if (isHidden) fs.setHiddenFileAttribute(outPath, false);
		if (!FileSystem.exists(outPath) || File.getContent(outPath) != text)
		{
			FileSystem.createDirectory(Path.directory(outPath));
			File.saveContent(outPath, text);
			r = true;
		}
		if (isHidden) fs.setHiddenFileAttribute(outPath, true);
		return r;
	}
}