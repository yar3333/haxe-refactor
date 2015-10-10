import hant.FileSystemTools;
import hant.Log;
import hant.Path;
import sys.FileSystem;
using StringTools;

class DirTools
{
	public static function parse(baseDir:String, verbose=true) : Array<String>
	{
		if (baseDir == null) return [];
		
		var baseDirs = [];
		
		Log.start("Prepare paths", verbose ? 1 : 1000);
		
		for (vdir in baseDir.split(";"))
		{
			vdir = Path.normalize(vdir);
			if (vdir.indexOf("*") < 0)
			{
				if (FileSystem.exists(vdir) && FileSystem.isDirectory(vdir))
				{
					Log.echo(vdir, verbose ? 1 : 1000);
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
							Log.echo(path, verbose ? 1 : 1000);
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
	
	public static function pathToPack(baseDir:String, oldPath:String, verbose:Bool) : { pack:String, filterDir:String }
	{
		baseDir = Path.normalize(baseDir);
		oldPath = Path.normalize(oldPath);
		
		if (oldPath.indexOf("/") >= 0)
		{
			for (dir in DirTools.parse(baseDir, false))
			{
				var path = dir == "." && !oldPath.startsWith("./") ? "./" + oldPath : oldPath;
				if (path.startsWith(dir + "/"))
				{
					var newPath = Path.withoutExtension(path.substr(dir.length + 1)).replace("/", ".");
					var filterDir = dir;
					
					if (verbose)
					{
						Log.echo("Convert disk path to type path: " + oldPath + " => " + newPath + "; filter directory: " + filterDir);
					}
					
					return { pack:newPath, filterDir:filterDir };
				}
			}
			
			return null;
		}
		
		return { pack:oldPath, filterDir:null };
	}
}