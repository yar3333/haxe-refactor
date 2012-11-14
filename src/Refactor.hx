
import hant.Hant;
import hant.Log;
import hant.PathTools;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class Refactor
{
	var log : Log;
	var hant : Hant;
	var baseDirs : Array<String>;
	
	public function new(log:Log, hant:Hant, baseDir:String)
	{
		this.log = log;
		this.hant = hant;
		
		log.start("Prepare paths");
		
		baseDirs = [];
		for (vdir in baseDir.split(";"))
		{
			vdir = PathTools.path2normal(vdir);
			if (vdir.indexOf("*") < 0)
			{
				if (FileSystem.exists(vdir) && FileSystem.isDirectory(vdir))
				{
					log.trace(vdir);
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
				var basePath = PathTools.path2normal(vdir.substr(0, n));
				var addPath = n + 1 < vdir.length ? vdir.substr(n + 1) : "";
				
				if (FileSystem.exists(basePath) && FileSystem.isDirectory(basePath))
				{
					for (dir in FileSystem.readDirectory(basePath))
					{
						var path = basePath + "/" + dir + addPath;
						if (FileSystem.exists(path) && FileSystem.isDirectory(path))
						{
							log.trace(path);
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
		
		log.finishOk();
	}
	
	public function renamePackage(srcPack:String, destPack:String)
	{
		for (baseDir in baseDirs)
		{
			log.start("Rename package: " + srcPack + " => " + destPack);
			
			var srcPath = baseDir + "/" + srcPack.replace(".", "/");
			var destPath = baseDir + "/" + destPack.replace(".", "/");
			
			if (FileSystem.exists(srcPath) && FileSystem.isDirectory(srcPath))
			{
				hant.findFiles(srcPath, function(path)
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
			
			log.trace("Replace in all *.hx and *.xml files: " + srcPack + " => " + destPack);
			
			hant.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx") || path.endsWith(".xml"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					var original = File.getContent(path);
					
					var text = original;
					text = replaceText(text, "(^|[^._a-zA-Z0-9])" + srcPack.replace(".", "[.]") + "\\b", "$1" + destPack);
					
					if (text != original)
					{
						saveFileText(path, text);
					}
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
			
			//log.trace("Rename file: "+ srcFile + " => " + destFile);
			
			if (renameFile(srcFile, destFile))
			{
				replaceInFile(destFile, [ 
					{ 
						  search: "\\bpackage\\s+" + src.full.replace(".", "[.]") + "\\s*;"
						, replacement: "package " + dest.full + ";"
					}
				]);
			}
			
			log.start("Replace in all haXe files: " + src.full + " => " + dest.full);
			hant.findFiles(baseDir, function(path:String)
			{
				if (path.endsWith(".hx"))
				{
					var localPath = path.substr(baseDir.length + 1);
					
					var original = File.getContent(path);
					
					var text = original;
					
					text = replaceText(text, "(^|[^._a-zA-Z0-9])" + src.full.replace(".", "[.]") + "\\b", "$1" + dest.full);
					
					if (src.name != dest.name)
					{
						if (new EReg("\\bpackage\\s+" + src.pack.replace(".", "[.]") + "\\s*;", "").match(text)
						 || new EReg("\\bimport\\s+" + src.full.replace(".", "[.]") + "\\s*;", "").match(text)
						) {
							
							log.trace(localPath + ": " + src.name + " => " + dest.name);
							text = replaceText(text, "(^|[^._a-zA-Z0-9])" + src.name + "\\b", "$1" + dest.name);
						}
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

			var srcVersionControlFolder = findVersionControlFolder(src);
			if (srcVersionControlFolder != null && findVersionControlFolder(dest) == srcVersionControlFolder)
			{
				var saveDir = Sys.getCwd();
				Sys.setCwd(Path.directory(src));
				if (srcVersionControlFolder.endsWith(".svn"))
				{
					Sys.command("svn", [ "mv", src, dest ]);
				}
				else
				if (srcVersionControlFolder.endsWith(".hg"))
				{
					Sys.command("hg", [ "mv", src, dest ]);
				}
				else
				if (srcVersionControlFolder.endsWith(".git"))
				{
					Sys.command("git", [ "mv", src, dest ]);
				}
				Sys.setCwd(saveDir);
			}
			else
			{
				FileSystem.rename(src, dest);
			}
			
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
	
	public function checkRules(rules:Array<{ search:String, replacement:String }>)
	{
		log.start("Check rules");
		for (rule in rules)
		{
			log.start(rule.search + " => " + rule.replacement);
			try
			{
				new EReg(rule.search, "g");
				log.finishOk();
			}
			catch (e:Dynamic)
			{
				log.finishFail();
				log.trace(e);
				return false;
			}
		}
		log.finishOk();
		return true;
	}
	
	/**
	 * Find and replace in file.
	 * @param	path		Path to file.
	 * @param	rules		Regular expression to find and replacement string. In replacements use $1-$9 to specify groups. Use '^' and 'v' between '$' and number to make uppercase/lowercase (for example, "x $^1 $v2 $3").
	 */
	public function replaceInFile(path:String, rules:Array<{ search:String, replacement:String }>)
	{
		log.start("Search in '" + path + "'");
		
		var original = File.getContent(path);
		
		var text = original;
		for (rule in rules)
		{
			text = replaceText(text, rule.search, rule.replacement);
		}
		
		if (text != original)
		{
			saveFileText(path, text);
		}
		
		log.finishOk();
	}
	
	public function replaceInFiles(filter:EReg, rules:Array<{ search:String, replacement:String }>)
	{
		for (baseDir in baseDirs)
		{
			log.start("Replace in '" + baseDir + "'");
			
			hant.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (filter.match(localPath))
				{
					replaceInFile(path, rules);
				}
			});
			
			log.finishOk();
		}
	}
	
	function saveFileText(path:String, text:String)
	{
		if (hant.getHiddenFileAttribute(path) == false)
		{
			File.saveContent(path, text);
		}
		else
		{
			hant.setHiddenFileAttribute(path, false);
			File.saveContent(path, text);
			hant.setHiddenFileAttribute(path, true);
		}
	}
	
	function replaceText(text:String, search:String, replacement:String)
	{
		if (replacement == "$-") replacement = "";
		
		var counter = 0;
		
		var r = new EReg(search, "g").customReplace(text, function(re)
		{
			var s = "";
			var i = 0;
			while (i < replacement.length)
			{
				var c = replacement.charAt(i++);
				if (c != "$")
				{
					s += c;
				}
				else
				{
					c = replacement.charAt(i++);
					if (c == "$")
					{
						s += "$";
					}
					else
					{
						var command = "";
						if ("0123456789".indexOf(c) < 0)
						{
							command = c;
							c = replacement.charAt(i++);
						}
						var number = Std.parseInt(c);
						var t = re.matched(number);
						switch(command)
						{
							case "^": t = t.toUpperCase();
							case "v": t = t.toLowerCase();
						}
						s += t;
					}
				}
			}
			
			log.trace(re.matched(0).replace("\r", "").replace("\n", "\\n") + " => " + s);
			
			return s;
		});
		
		return r;
	}
}