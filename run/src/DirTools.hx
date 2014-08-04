package ;

import hant.FileSystemTools;
import hant.Log;
import hant.PathTools;
import sys.FileSystem;

class DirTools
{
	public static function parse(baseDir:String, log:Log, verbose:Bool) : Array<String>
	{
		if (baseDir == null) return [];
		
		var baseDirs = [];
		
		if (verbose) log.start("Prepare paths");
		
		for (vdir in baseDir.split(";"))
		{
			vdir = PathTools.normalize(vdir);
			if (vdir.indexOf("*") < 0)
			{
				if (FileSystem.exists(vdir) && FileSystem.isDirectory(vdir))
				{
					if (verbose) log.trace(vdir);
					baseDirs.push(vdir);
				}
				else
				{
					log.trace("Directory '" + vdir + "' is not found.");
				}
			}
			else
			{
				var n = vdir.indexOf("*");
				var basePath = PathTools.normalize(vdir.substr(0, n));
				var addPath = n + 1 < vdir.length ? vdir.substr(n + 1) : "";
				
				if (FileSystem.exists(basePath) && FileSystem.isDirectory(basePath))
				{
					for (dir in FileSystem.readDirectory(basePath))
					{
						var path = basePath + "/" + dir + addPath;
						if (FileSystem.exists(path) && FileSystem.isDirectory(path))
						{
							if (verbose) log.trace(path);
							baseDirs.push(path);
						}
					}
				}
				else
				{
					log.trace("Directory '" + basePath + "' is not found.");
				}
			}
		}
		
		if (verbose) log.finishOk();
		
		return baseDirs;
	}
}