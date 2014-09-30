import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class TextFile
{
	var fs:FileSystemTools;
	var inpPath : String;
	var outPath : String;
	var verbose : Bool;
	var log : Log;
	
	public function new(fs:FileSystemTools, inpPath:String, outPath:String, verbose:Bool, log:Log)
	{
		this.fs = fs;
		this.inpPath = inpPath;
		this.outPath = outPath;
		this.verbose = verbose;
		this.log = log;
	}
	
	public function process(f:String->String)
	{
		var text = File.getContent(inpPath);
		var original = text;
		
		var isWinLineEndStyle = text.indexOf("\r\n") >= 0;
		if (isWinLineEndStyle) text = text.replace("\r\n", "\n");
		
		var isMacLineEndStyle = !isWinLineEndStyle && text.indexOf("\r") >= 0;
		if (isMacLineEndStyle) text = text.replace("\r", "\n");
		
		text = f(text);
		
		if (text != null)
		{
			if (isMacLineEndStyle) text = text.replace("\n", "\r");
			else
			if (isWinLineEndStyle) text = text.replace("\n", "\r\n");
			
			if (text != original && save(outPath, text, true))
			{
				if (verbose) log.trace("Fixed: " + outPath);
			}
		}
	}
	
	public function save(outPath:String, text:String, force=false) : Bool
	{
		var r = false;
		var isHidden = fs.getHiddenFileAttribute(outPath);
		if (isHidden) fs.setHiddenFileAttribute(outPath, false);
		if (force || !FileSystem.exists(outPath) || File.getContent(outPath) != text)
		{
			FileSystem.createDirectory(Path.directory(outPath));
			File.saveContent(outPath, text);
			r = true;
		}
		if (isHidden) fs.setHiddenFileAttribute(outPath, true);
		return r;
	}
}