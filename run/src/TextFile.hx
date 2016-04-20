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
	var baseVerboseLevel : Int;
	var originalLineEndings : String;
	
	public var lineEndings : String;
	
	public var inpPath(default, null) : String;
	public var outPath(default, null) : String;
	
	public var text(default, null) : String;
	
	public function new(inpPath:String, outPath:String, baseVerboseLevel:Int)
	{
		this.inpPath = inpPath;
		this.outPath = outPath;
		this.baseVerboseLevel = baseVerboseLevel;
		
		text = File.getContent(inpPath);
		
		var isWindowsLineEndings = text.indexOf("\r\n") >= 0;
		if (isWindowsLineEndings) text = text.replace("\r\n", "\n");
		
		var isMacLineEndings = !isWindowsLineEndings && text.indexOf("\r") >= 0;
		if (isMacLineEndings) text = text.replace("\r", "\n");
		
		if (isWindowsLineEndings)
		{
			lineEndings = "windows";
		}
		else
		if (isMacLineEndings)
		{
			lineEndings = "mac";
		}
		else
		{
			lineEndings = "unix";
		}
		originalLineEndings = lineEndings;
	}
	
	public function process(f:String->FileApi->String)
	{
		var text = f(this.text, cast this);
		
		if (text != null)
		{
			if (save(outPath, text))
			{
				Log.echo("Fixed: " + outPath, baseVerboseLevel);
			}
		}
	}
	
	function save(outPath:String, text:String) : Bool
	{
		if (originalLineEndings == lineEndings && inpPath == outPath && text == this.text) return false;
		
		this.text = text;		
		
		switch (lineEndings)
		{
			case "windows":
				text = text.replace("\n", "\r\n");
				
			case "mac":
				text = text.replace("\n", "\r");
		}
		
		var r = false;
		var isHidden = FileSystemTools.getHiddenFileAttribute(outPath);
		if (isHidden) FileSystemTools.setHiddenFileAttribute(outPath, false);
		if (!FileSystem.exists(outPath) || File.getContent(outPath) != text)
		{
			var dir = Path.directory(outPath);
			if (dir != "" && !FileSystem.exists(dir)) FileSystem.createDirectory(dir);
			File.saveContent(outPath, text);
			r = true;
		}
		if (isHidden) FileSystemTools.setHiddenFileAttribute(outPath, true);
		return r;
	}
}