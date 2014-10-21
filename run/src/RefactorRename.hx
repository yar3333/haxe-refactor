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
			log.start("Rename package '" + srcPack + "' => '" + destPack + "'");
			
			var srcPath = baseDir + "/" + srcPack.replace(".", "/");
			var destPath = baseDir + "/" + destPack.replace(".", "/");
			
			if (FileSystem.exists(srcPath) && FileSystem.isDirectory(srcPath))
			{
				fs.findFiles(srcPath, function(path)
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
			
			log.trace("Replace in all *.hx and *.xml files '" + srcPack + "' => '" + destPack + "'");
			
			fs.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx") || path.endsWith(".xml"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					if (verbose) log.start("Process file '" + localPath + "'");
					
					new TextFile(fs, path, path, verbose, log).process(function(text, _)
					{
						var re = new Regex("/(^|[^._a-zA-Z0-9])" + srcPack.replace(".", "[.]") + "\\b/$1" + destPack + "/");
						return re.replace(text, verbose ? function(s) log.trace(s) : null);
					});
					
					if (verbose) log.finishOk();
				}
			});
			
			log.finishOk();
		}
	}
	
	public function renameClass(src:ClassPath, dest:ClassPath)
	{
		for (baseDir in baseDirs)
		{
			log.start("Rename class: " + src.full + " => " + dest.full);
			
			var srcFile = baseDir + "/" + src.getFilePath();
			var destFile = baseDir + "/" + dest.getFilePath();
			
			if (renameFile(srcFile, destFile))
			{
				replaceInFile(destFile, [ new Regex("/\\bpackage\\s+" + src.full.replace(".", "[.]") + "\\s*;/package " + dest.full + ";/") ], destFile, true, true);
			}
			
			log.start("Replace in all haxe files: " + src.full + " => " + dest.full);
			fs.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					new TextFile(fs, path, path, verbose, log).process(function(text, _)
					{
						var packageOrImport = 
								new EReg("\\bpackage\\s+" + src.pack.replace(".", "[.]") + "\\s*;", "").match(text)
							 || new EReg("\\bimport\\s+" + src.full.replace(".", "[.]") + "\\s*;", "").match(text);
						
						text = new Regex("/(^|[^._a-zA-Z0-9])" + src.full.replace(".", "[.]") + "\\b/$1" + dest.full + "/").replace(text, verbose ? function(s) log.trace(s) : null);
						
						if (packageOrImport && src.name != dest.name)
						{
							if (verbose) log.trace(localPath + ": " + src.name + " => " + dest.name);
							text = new Regex("/(^|[^._a-zA-Z0-9])" + src.name + "\\b/$1" + dest.name + "/").replace(text, verbose ? function(s) log.trace(s) : null);
						}
						
						return text;
					});
				}
			});
			log.finishOk();
			
			log.finishOk();
		}
	}
	
	public function renameFile(src:String, dest:String) : Bool
	{
		if (FileSystem.exists(src) && !FileSystem.exists(dest))
		{
			log.start("Rename file " + src + " => " + dest);
			
			fs.createDirectory(Path.directory(dest));
			FileSystem.rename(src, dest);
			
			log.finishOk();
			
			return true;
		}
		return false;
	}
}