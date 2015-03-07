import hant.FileSystemTools;
import hant.Log;
import hant.Path;
import sys.FileSystem;
using StringTools;

class DirTools
{
	public static function parse(baseDir:String) : Array<String>
	{
		if (baseDir == null) return [];
		
		var baseDirs = [];
		
		Log.start("Prepare paths");
		
		for (vdir in baseDir.split(";"))
		{
			vdir = Path.normalize(vdir);
			if (vdir.indexOf("*") < 0)
			{
				if (FileSystem.exists(vdir) && FileSystem.isDirectory(vdir))
				{
					Log.echo(vdir);
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
							Log.echo(path);
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
		
		Log.finishSuccess();
		
		return baseDirs;
	}
	
	public static function pathToPack(baseDir:String, path:String, verbose:Bool) : { pack:String, filterDir:String }
	{
		baseDir = baseDir.replace("\\", "/");
		path = path.replace("\\", "/");
		
		var filterDir = null;
		
		if (path.indexOf("/") >= 0)
		{
			var baseDirFound = false;
			for (dir in DirTools.parse(baseDir))
			{
				if (dir == "." && !path.startsWith("./")) path = "./" + path;
				
				if (path.startsWith(dir + "/"))
				{
					baseDirFound = true;
					var oldPath = path;
					path = Path.withoutExtension(path.substr(dir.length + 1)).replace("/", ".");
					filterDir = path.substring(0, dir.length);
					if (verbose)
					{
						Log.echo("Convert disk path to type path: " + oldPath + " => " + path + "; filter directory: " + filterDir);
					}
				}
			}
			
			if (!baseDirFound) return null;
		}
		
		return { pack:path, filterDir:filterDir };
	}
}