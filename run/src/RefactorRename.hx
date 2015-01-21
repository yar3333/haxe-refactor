import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
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
					
					if (verbose) Log.start("Process file '" + localPath + "'");
					
					new TextFile(path, path, verbose).process(function(text, _)
					{
						var re = new Regex("/(^|[^._a-zA-Z0-9])" + srcPack.replace(".", "[.]") + "\\b/$1" + destPack + "/");
						return re.replace(text, verbose ? function(s) Log.echo(s) : null);
					});
					
					if (verbose) Log.finishSuccess();
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
					
					new TextFile(path, path, verbose).process(function(text, _)
					{
						var packageOrImport = 
								new EReg("\\bpackage\\s+" + src.pack.replace(".", "[.]") + "\\s*;", "").match(text)
							 || new EReg("\\bimport\\s+" + src.full.replace(".", "[.]") + "\\s*;", "").match(text);
						
						text = new Regex("/(^|[^._a-zA-Z0-9])" + src.full.replace(".", "[.]") + "\\b/$1" + dest.full + "/").replace(text, verbose ? function(s) Log.echo(s) : null);
						
						if (packageOrImport && src.name != dest.name)
						{
							if (verbose) Log.echo(localPath + ": " + src.name + " => " + dest.name);
							text = new Regex("/(^|[^._a-zA-Z0-9])" + src.name + "\\b/$1" + dest.name + "/").replace(text, verbose ? function(s) Log.echo(s) : null);
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
			FileSystem.rename(src, dest);
			
			Log.finishSuccess();
			
			return true;
		}
		return false;
	}
}