import hant.FileSystemTools;
import hant.Log;
import hant.Path;
import sys.FileSystem;
using StringTools;

class DirTools
{
	public static function parse(baseDir:String, verbose:Bool) : Array<String>
	{
		if (baseDir == null) return [];
		
		var baseDirs = [];
		
		if (verbose) Log.start("Prepare paths");
		
		for (vdir in baseDir.split(";"))
		{
			vdir = Path.normalize(vdir);
			if (vdir.indexOf("*") < 0)
			{
				if (FileSystem.exists(vdir) && FileSystem.isDirectory(vdir))
				{
					if (verbose) Log.echo(vdir);
					baseDirs.push(vdir);
				}
				else
				{
					Log.echo("Directory '" + vdir + "' is not found.");
				}
			}
			else
			{
				var n = vdir.indexOf("*");
				var basePath = Path.normalize(vdir.substr(0, n));
				var addPath = n + 1 < vdir.length ? vdir.substr(n + 1) : "";
				
				if (FileSystem.exists(basePath) && FileSystem.isDirectory(basePath))
				{
					for (dir in FileSystem.readDirectory(basePath))
					{
						var path = basePath + "/" + dir + addPath;
						if (FileSystem.exists(path) && FileSystem.isDirectory(path))
						{
							if (verbose) Log.echo(path);
							baseDirs.push(path);
						}
					}
				}
				else
				{
					Log.echo("Directory '" + basePath + "' is not found.");
				}
			}
		}
		
		if (verbose) Log.finishSuccess();
		
		return baseDirs;
	}
	
	public static function pathToPack(baseDir:String, path:String, verbose:Bool) : String
	{
		baseDir = baseDir.replace("\\", "/");
		path = path.replace("\\", "/");
		
		if (path.indexOf("/") >= 0)
		{
			var baseDirFound = false;
			for (dir in DirTools.parse(baseDir, verbose))
			{
				if (dir == "." && !path.startsWith("./")) path = "./" + path;
				
				if (path.startsWith(dir + "/"))
				{
					baseDirFound = true;
					var oldPath = path;
					path = Path.withoutExtension(path.substr(dir.length + 1)).replace("/", ".");
					if (verbose) Log.echo("Convert disk path to package/class: " + oldPath + " => " + path);
				}
			}
			
			if (!baseDirFound) return null;
		}
		
		return path;
	}
}