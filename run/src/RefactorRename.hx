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
	public function renamePackage(srcPack:String, destPack:String, srcFilterDir:String, destFilterDir:String, baseLogLevel:Int)
	{
		Log.start("Rename package '" + srcPack + "' => '" + destPack + "'", baseLogLevel);
		
		for (baseDir in (srcFilterDir != null ? [srcFilterDir] : baseDirs))
		{
			var srcPath = baseDir + "/" + srcPack.replace(".", "/");
			var destPath = (destFilterDir != null ? destFilterDir : baseDir) + "/" + destPack.replace(".", "/");
			
			if (FileSystem.exists(srcPath) && FileSystem.isDirectory(srcPath))
			{
				FileSystemTools.findFiles(srcPath, function(path)
				{
					if (path.endsWith(".hx"))
					{
						renameClass
						(
							ClassPath.fromFilePath(baseDir, path),
							ClassPath.fromFilePath(baseDir, destPath + path.substr(srcPath.length)),
							srcFilterDir,
							destFilterDir,
							baseLogLevel + 1
						);
					}
					else
					{
						renameFile(srcPath, destPath);
					}
				});
			}
		}
		
		Log.start("Replace in files: " + srcPack + " => " + destPack, baseLogLevel + 1);
		for (baseDir in baseDirs)
		{
			FileSystemTools.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx") || path.endsWith(".xml"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					Log.start("Process file '" + localPath + "'", baseLogLevel + 2);
					
					new TextFile(path, path, baseLogLevel + 3).process(function(text, _)
					{
						var re = new Regex("/(^|[^._a-zA-Z0-9])" + srcPack.replace(".", "[.]") + "\\b/$1" + destPack + "/");
						return re.replace(text, Log.echo.bind(_, baseLogLevel + 3));
					});
					
					Log.finishSuccess();
				}
			});
		}
		Log.finishSuccess();
		
		Log.finishSuccess();
	}
	
	public function renameClass(src:ClassPath, dest:ClassPath, srcFilterDir:String, destFilterDir:String, baseLogLevel:Int)
	{
		Log.start("Rename class: " + src.full + " => " + dest.full, baseLogLevel);
		
		for (baseDir in (srcFilterDir != null ? [srcFilterDir] : baseDirs))
		{
			var srcFile = baseDir + "/" + src.getFilePath();
			var destFile = (destFilterDir != null ? destFilterDir : baseDir) + "/" + dest.getFilePath();
			
			if (renameFile(srcFile, destFile))
			{
				replaceInFile
				(
					destFile,
					[ new Regex("/\\bpackage\\s+" + src.full.replace(".", "[.]") + "\\s*;/package " + dest.full + ";/") ],
					destFile,
					true,
					true,
					baseLogLevel + 1
				);
			}
		}
		
		Log.start("Replace in files: " + src.full + " => " + dest.full, baseLogLevel + 1);
		for (baseDir in baseDirs)
		{
			FileSystemTools.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					new TextFile(path, path, baseLogLevel + 2).process(function(text, _)
					{
						var packageOrImport = 
								new EReg("\\bpackage\\s+" + src.pack.replace(".", "[.]") + "\\s*;", "").match(text)
							 || new EReg("\\bimport\\s+" + src.full.replace(".", "[.]") + "\\s*;", "").match(text);
						
						text = new Regex("/(^|[^._a-zA-Z0-9])" + src.full.replace(".", "[.]") + "\\b/$1" + dest.full + "/")
							.replace(text, Log.echo.bind(_, baseLogLevel + 3));
						
						if (packageOrImport && src.name != dest.name)
						{
							Log.echo(localPath + ": " + src.name + " => " + dest.name, baseLogLevel + 2);
							text = new Regex("/(^|[^._a-zA-Z0-9])" + src.name + "\\b/$1" + dest.name + "/")
								.replace(text, Log.echo.bind(_, baseLogLevel + 3));
						}
						
						return text;
					});
				}
			});
		}
		Log.finishSuccess();
		
		Log.finishSuccess();
	}
	
	public function renameFile(src:String, dest:String) : Bool
	{
		if (FileSystem.exists(src) && !FileSystem.exists(dest))
		{
			Log.start("Rename file " + src + " => " + dest);
			
			FileSystemTools.createDirectory(Path.directory(dest));
			
			var rootSrc = getRoot(src);
			var rootDst = rootSrc != null ? getRoot(dest) : null;
			if (rootSrc != null && rootDst != null && rootSrc == rootDst)
			{
				switch (roots.get(rootSrc))
				{
					case "hg":
						if (!Process.run("hg", [ "status", src ]).output.startsWith("?"))
						{
							if (Sys.command("hg", [ "mv", src, dest ]) == 0)
							{
								Log.finishSuccess("hg");
								return true;
							}
						}
					case "git":
						if (Sys.command("git", [ "ls-files", src, "--error-unmatch" ]) == 0)
						{
							if (Sys.command("git", [ "mv", src, dest ]) == 0)
							{
								Log.finishSuccess("git");
								return true;
							}
						}
				}
			}
			
			try
			{
				FileSystem.rename(src, dest);
				Log.finishSuccess();
				return true;
			}
			catch (e:Dynamic)
			{
				Log.finishFail(Std.string(e));
			}
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
			hg = Process.run("hg", [ "--version" ], null, false, false).exitCode == 0;
		}
		
		if (git == null)
		{
			git = Process.run("git", [ "--version" ], null, false, false).exitCode == 0;
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