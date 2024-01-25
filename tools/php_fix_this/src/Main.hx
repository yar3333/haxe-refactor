import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class Main
{
    static function main()
    {
		var args = Sys.args();
		
		if (args.length != 1)
		{
			Log.echo("Usage: php_fix_this <src_folder_or_file>");
		}
		else
		{
			if (FileSystem.exists(args[0]))
			{
				if (FileSystem.isDirectory(args[0]))
				{
					FileSystemTools.findFiles(args[0], processFile);
				}
				else
				{
					processFile(args[0]);
				}
			}
		}
	}
	
	static function processFile(file:String)
	{
		var a = File.getContent(file).replace("\r\n", "\n").replace("\r", "\n");
		var b = PhpFixThis.fix(a);
		if (b != a)
		{
			Log.echo("FIXED");
			File.saveContent(file, b);
		}
	}
}
