import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class TextFile
{
	var fs:FileSystemTools;
	var path : String;
	var verbose : Bool;
	var log : Log;
	
	public function new(fs:FileSystemTools, path:String, verbose:Bool, log:Log)
	{
		this.fs = fs;
		this.path = path;
		this.verbose = verbose;
		this.log = log;
	}
	
	public function process(f:String->String)
	{
		var text = File.getContent(path);
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
			
			if (text != original && save(fs, path, text, true))
			{
				if (verbose) log.trace("Fixed: " + path);
			}
		}
	}
	
	public static function save(fs:FileSystemTools, path:String, text:String, force=false) : Bool
	{
		var r = false;
		var isHidden = fs.getHiddenFileAttribute(path);
		if (isHidden) fs.setHiddenFileAttribute(path, false);
		if (force || !FileSystem.exists(path) || File.getContent(path) != text)
		{
			FileSystem.createDirectory(Path.directory(path));
			File.saveContent(path, text);
			r = true;
		}
		if (isHidden) fs.setHiddenFileAttribute(path, true);
		return r;
	}
}