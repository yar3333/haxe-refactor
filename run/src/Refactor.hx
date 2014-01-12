
import hant.FileSystemTools;
import hant.Log;
import hant.PathTools;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class Refactor
{
	var log : Log;
	var fs : FileSystemTools;
	var baseDirs : Array<String>;
	var outDir : String;
	var verbose : Bool;
	
	public function new(log:Log, fs:FileSystemTools, baseDir:String, outDir:String, verbose:Bool)
	{
		this.log = log;
		this.fs = fs;
		this.outDir = outDir != null && outDir != "" ? Path.addTrailingSlash(outDir) : outDir;
		this.verbose = verbose;
		
		if (verbose) log.start("Prepare paths");
		
		baseDirs = [];
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
	}
	
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
						renameClass(
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
					var original = File.getContent(path);
					var text = new Rule("/(^|[^._a-zA-Z0-9])" + srcPack.replace(".", "[.]") + "\\b/$1" + destPack + "/").apply(original, verbose ? log : null);
					saveFileText(path, text);
					log.trace("Fixed: " + localPath);
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
				replaceInFile(destFile, [ new Rule("/\\bpackage\\s+" + src.full.replace(".", "[.]") + "\\s*;/package " + dest.full + ";/") ], destFile);
			}
			
			log.start("Replace in all haXe files: " + src.full + " => " + dest.full);
			fs.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					var original = File.getContent(path);
					
					var text = original;
					
					var packageOrImport = 
						    new EReg("\\bpackage\\s+" + src.pack.replace(".", "[.]") + "\\s*;", "").match(text)
						 || new EReg("\\bimport\\s+" + src.full.replace(".", "[.]") + "\\s*;", "").match(text);
					
					text = new Rule("/(^|[^._a-zA-Z0-9])" + src.full.replace(".", "[.]") + "\\b/$1" + dest.full + "/").apply(text, verbose != null ? log : null);
					
					if (packageOrImport && src.name != dest.name)
					{
						if (verbose) log.trace(localPath + ": " + src.name + " => " + dest.name);
						text = new Rule("/(^|[^._a-zA-Z0-9])" + src.name + "\\b/$1" + dest.name + "/").apply(text, verbose != null ? log : null);
					}
					
					if (text != original)
					{
						log.trace("Fixed: " + localPath);
						saveFileText(path, text);
					}
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
	
	function findVersionControlFolder(path:String) : String
	{
		var p = path.split("/");
		for (i in 1...p.length)
		{
			for (folder in [ ".svn", ".hg", ".git" ])
			{
				var cur = p.slice(0, i).join("/") + "/" + folder;
				if (FileSystem.exists(cur) && FileSystem.isDirectory(cur)) return cur;
			}
		}
		return null;
	}
	
	public function checkRules(rules:Array<Rule>)
	{
		if (verbose) log.start("Check rules");
		for (rule in rules)
		{
			if (verbose) log.start(rule.search + " => " + rule.replacement);
			try
			{
				new EReg(rule.search, "g");
				if (verbose) log.finishOk();
			}
			catch (e:Dynamic)
			{
				if (verbose) log.finishFail();
				log.trace(e);
				return false;
			}
		}
		if (verbose) log.finishOk();
		return true;
	}
	
	public function replaceInFile(inpPath:String, rules:Array<Rule>, outPath:String)
	{
		if (verbose) log.start("Search in '" + inpPath + "'");
		
		var original = File.getContent(inpPath);
		
		var text = original;
		for (rule in rules)
		{
			text = rule.apply(text, verbose != null ? log : null);
		}
		
		saveFileText(outPath, text);
		if (!verbose) log.trace("Fixed: " + inpPath);
		
		if (verbose) log.finishOk();
	}
	
	public function replaceInFiles(filter:EReg, changeFileName:Rule, rules:Array<Rule>)
	{
		for (baseDir in baseDirs)
		{
			log.start("Replace in '" + baseDir + "'");
			
			fs.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (filter.match(localPath))
				{
					if (outDir == null)
					{
						replaceInFile(path, rules, Path.directory(path) + changeFileName.apply(Path.withoutDirectory(path)));
					}
					else
					{
						var localDir = Path.directory(localPath);
						replaceInFile(path, rules, outDir + (localDir != "" ? Path.addTrailingSlash(localDir) : "") + changeFileName.apply(Path.withoutDirectory(localPath)));
					}
				}
			});
			
			log.finishOk();
		}
	}
	
	function saveFileText(path:String, text:String)
	{
		var isHidden = fs.getHiddenFileAttribute(path);
		if (isHidden) fs.setHiddenFileAttribute(path, false);
		if (!FileSystem.exists(path) || File.getContent(path) != text)
		{
			File.saveContent(path, text);
		}
		if (isHidden) fs.setHiddenFileAttribute(path, true);
	}
}