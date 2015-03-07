import hant.FileSystemTools;
import hant.Log;
import hant.Process;
import hant.Path;
import stdlib.Regex;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class RefactorRename extends RefactorReplace
{
	public function renamePackage(srcPack:String, destPack:String)
	{
		for (baseDir in baseDirs)
		{
			Log.start("Rename package '" + srcPack + "' => '" + destPack + "'");
			
			var srcPath = baseDir + "/" + srcPack.replace(".", "/");
			var destPath = baseDir + "/" + destPack.replace(".", "/");
			
			if (FileSystem.exists(srcPath) && FileSystem.isDirectory(srcPath))
			{
				FileSystemTools.findFiles(srcPath, function(path)
				{
					if (path.endsWith(".hx"))
					{
						renameClass
						(
							  ClassPath.fromFilePath(baseDir, path)
							, ClassPath.fromFilePath(baseDir, destPath + path.substr(srcPath.length))
						);
					}
					else
					{
						renameFile(srcPath, destPath);
					}
				});
			}
			
			Log.echo("Replace in all *.hx and *.xml files '" + srcPack + "' => '" + destPack + "'");
			
			FileSystemTools.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx") || path.endsWith(".xml"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					if (verboseLevel > 1) Log.start("Process file '" + localPath + "'");
					
					new TextFile(path, path, verboseLevel > 1).process(function(text, _)
					{
						var re = new Regex("/(^|[^._a-zA-Z0-9])" + srcPack.replace(".", "[.]") + "\\b/$1" + destPack + "/");
						return re.replace(text, verboseLevel > 2 ? function(s) Log.echo(s) : null);
					});
					
					if (verboseLevel > 1) Log.finishSuccess();
				}
			});
			
			Log.finishSuccess();
		}
	}
	
	public function renameClass(src:ClassPath, dest:ClassPath)
	{
		for (baseDir in baseDirs)
		{
			Log.start("Rename class: " + src.full + " => " + dest.full);
			
			var srcFile = baseDir + "/" + src.getFilePath();
			var destFile = baseDir + "/" + dest.getFilePath();
			
			if (renameFile(srcFile, destFile))
			{
				replaceInFile(destFile, [ new Regex("/\\bpackage\\s+" + src.full.replace(".", "[.]") + "\\s*;/package " + dest.full + ";/") ], destFile, true, true);
			}
			
			Log.start("Replace in all haxe files: " + src.full + " => " + dest.full);
			FileSystemTools.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					new TextFile(path, path, verboseLevel > 1).process(function(text, _)
					{
						var packageOrImport = 
								new EReg("\\bpackage\\s+" + src.pack.replace(".", "[.]") + "\\s*;", "").match(text)
							 || new EReg("\\bimport\\s+" + src.full.replace(".", "[.]") + "\\s*;", "").match(text);
						
						text = new Regex("/(^|[^._a-zA-Z0-9])" + src.full.replace(".", "[.]") + "\\b/$1" + dest.full + "/").replace(text, verboseLevel > 2 ? function(s) Log.echo(s) : null);
						
						if (packageOrImport && src.name != dest.name)
						{
							if (verboseLevel > 2) Log.echo(localPath + ": " + src.name + " => " + dest.name);
							text = new Regex("/(^|[^._a-zA-Z0-9])" + src.name + "\\b/$1" + dest.name + "/").replace(text, verboseLevel > 2 ? function(s) Log.echo(s) : null);
						}
						
						return text;
					});
				}
			});
			Log.finishSuccess();
			
			Log.finishSuccess();
		}
	}
	
	public function renameFile(src:String, dest:String) : Bool
	{
		if (FileSystem.exists(src) && !FileSystem.exists(dest))
		{
			Log.start("Rename file " + src + " => " + dest);
			
			FileSystemTools.createDirectory(Path.directory(dest));
			
			var moved = false;
			
			var rootSrc = getRoot(src);
			var rootDst = rootSrc != null ? getRoot(dest) : null;
			if (rootSrc != null && rootDst != null && rootSrc == rootDst)
			{
				switch (roots.get(rootSrc))
				{
					case "hg":
						if (!Process.run("hg", [ "status", src ]).output.startsWith("?"))
						{
							moved = Sys.command("hg", [ "mv", src, dest ]) == 0;
						}
					case "git":
						if (Sys.command("git", [ "ls-files", src, "--error-unmatch" ]) == 0)
						{
							moved = Sys.command("git", [ "mv", src, dest ]) == 0;
						}
				}
			}
			
			if (!moved) FileSystem.rename(src, dest);
			
			Log.finishSuccess();
			
			return true;
		}
		return false;
	}
	
	static var hg : Bool = null;
	static var git : Bool = null;
	static var roots = new Map<String, String>(); // "path/to/root" => "hg"/"git"
	
	static function getRoot(filePath:String) : String
	{
		var fullPath = Path.normalize(FileSystem.fullPath(filePath));
		
		for (root in roots.keys())
		{
			if (fullPath.startsWith(root + "/")) return root;
		}
		
		if (hg == null)
		{
			hg = Sys.command("hg", [ "--version" ]) == 0;
			trace("DETECT Mercurial = " + hg);
		}
		
		if (git == null)
		{
			git = Sys.command("git", [ "--version" ]) == 0;
			trace("DETECT Git = " + git);
		}
		
		if (hg)
		{
			var r = Process.run("hg", [ "--cwd", Path.directory(filePath), "root" ], null, false, false);
			if (r.exitCode == 0)
			{
				var root = Path.normalize(r.output);
				roots.set(root, "hg");
				return root;
			}
		}
		
		if (git)
		{
			var saveCwd = Sys.getCwd();
			Sys.setCwd(Path.directory(filePath));
			var r = Process.run("git", [ "rev-parse", "--show-toplevel" ], null, false, false);
			Sys.setCwd(saveCwd);
			if (r.exitCode == 0)
			{
				var root = Path.normalize(r.output);
				roots.set(root, "git");
				return root;
			}
		}
		
		return null;
	}
}