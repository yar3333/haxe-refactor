import hant.PathTools;
import neko.Lib;
import sys.FileSystem;
using StringTools;
using Lambda;

class BaseCommands
{
	function filterToRegex(s:String) : String
	{
		if (s == "*" || s == "*.*") return ".";
		if (~/^[*][.][a-z0-9_-]+(?:\s*;\s*[*][.][a-z0-9_-]+)*$/i.match(s))
		{
			var exts = s.split(";").map(function(s) return s.trim().substr("*.".length));
			return "[.](?:" + exts.join("|") + ")$";
		}
		return s;
	}
	
	function fail(message:String)
	{
		Lib.println("ERROR: " + message);
		Sys.exit(1);
	}
	
	function pathToPack(srcDirs:String, path:String) : String
	{
		path = PathTools.normalize(path);
		if (path.indexOf("/") < 0 && !path.endsWith(".hx")) return path;
		
		if (path.endsWith(".hx")) path = path.substr(0, path.length - ".hx".length);
		
		for (srcDir in srcDirs.split(";").map(function(e) return PathTools.normalize(e)))
		{
			if (path.startsWith(srcDir + "/"))
			{
				return path.substr(srcDir.length + 1).replace("/", ".");
			}
		}
		
		fail("Path '" + path + "' is not in source directories.");
		return null;
	}
	
	function getRulesFilePath(exeDir:String, path:String) : String
	{
		if (!FileSystem.exists(path))
		{
			var alt = haxe.io.Path.join([ exeDir, "rules", path ]);
			if (FileSystem.exists(alt) && !FileSystem.isDirectory(alt))
			{
				path = alt;
			}
		}
		
		if (!FileSystem.exists(path)) fail("Could't find rulesFile '" + path + "'.");
		
		return path;
	}
}