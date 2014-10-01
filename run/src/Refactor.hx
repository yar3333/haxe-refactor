import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import stdlib.Regex;
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
		
		baseDirs = DirTools.parse(baseDir, log, verbose);
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
			
			log.start("Replace in all haXe files: " + src.full + " => " + dest.full);
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
	
	public function checkRules(rules:Array<Regex>)
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
	
	public function replaceInFile(inpPath:String, rules:Array<Regex>, outPath:String, excludeStrings:Bool, excludeComments:Bool)
	{
		if (verbose) log.start("Search in '" + inpPath + "'");
		
		new TextFile(fs, inpPath, outPath, verbose, log).process(function(text, _)
		{
			if (!excludeStrings && !excludeComments)
			{
				for (rule in rules)
				{
					text = rule.replace(text, verbose ? function(s) log.trace(s) : null);
				}
			}
			else
			{
				for (rule in rules)
				{
					var r = "";
					
					var reStr = (excludeStrings ? "(\"|')(?:\\\\.|.)*?\\1" : "({9a5a7986-d5e5-4c5e-92fc-ee557254d67f})")
							  + "|"
							  + (excludeComments ? "(/\\*.*?\\*/|^//.*?$)" : "({9a5a7986-d5e5-4c5e-92fc-ee557254d67f})");
					var re = new EReg(reStr, "m");
					var i = 0; while (re.matchSub(text, i))
					{
						var p = re.matchedPos();
						
						if (excludeStrings && re.matched(1) != null)
						{
							r += rule.replace(text.substr(i, p.pos - i + 1), verbose ? function(s) log.trace(s) : null);
							r += re.matched(0).substr(1, p.len - 2);
							i = p.pos + p.len - 1;
						}
						else
						{
							r += rule.replace(text.substr(i, p.pos - i), verbose ? function(s) log.trace(s) : null);
							r += re.matched(0);
							i = p.pos + p.len;
						}
					}
					r += rule.replace(text.substr(i), verbose ? function(s) log.trace(s) : null);
					text = r;
				}
			}
			return text;
		});
		
		if (verbose) log.finishOk();
	}
	
	public function replaceInFiles(filter:EReg, changeFileName:Regex, rules:Array<Regex>, excludeStrings:Bool, excludeComments:Bool)
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
						replaceInFile(path, rules, Path.directory(path) + "/" + changeFileName.replace(Path.withoutDirectory(path)), excludeStrings, excludeComments);
					}
					else
					{
						var localDir = Path.directory(localPath);
						replaceInFile(path, rules, outDir + (localDir != "" ? Path.addTrailingSlash(localDir) : "") + changeFileName.replace(Path.withoutDirectory(localPath)), excludeStrings, excludeComments);
					}
				}
			});
			
			log.finishOk();
		}
	}
	
	public function reindent(filter:EReg, oldTabSize:Int, oldIndentSize:Int, newTabSize:Int, newIndentSize:Int, shiftSize:Int)
	{
		for (baseDir in baseDirs)
		{
			log.start("Reindent in '" + baseDir + "'");
			
			fs.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (filter.match(localPath))
				{
					reindentFile(path, oldTabSize, oldIndentSize, newTabSize, newIndentSize, shiftSize);
				}
			});
			
			log.finishOk();
		}
	}
	
	public function reindentFile(path:String, oldTabSize:Int, oldIndentSize:Int, newTabSize:Int, newIndentSize:Int, shiftSize:Int) : Void
	{
		if (!FileSystem.exists(path) || FileSystem.isDirectory(path))
		{
			log.start("Reindent in '" + path + "'");
			log.finishFail("File not found.");
		}
		
		var oldText = File.getContent(path);
		oldText = oldText.replace("\r\n", "\n").replace("\r", "\n");
		
		var lines = oldText.split("\n");
		for (i in 0...lines.length)
		{
			var oldLine = lines[i];
			var newLine = "";
			var oldPos = 0;
			var j = 0; while (j < oldLine.length)
			{
				if      (oldLine.charAt(j) == " ")  oldPos++;
				else if (oldLine.charAt(j) == "\t") oldPos = (Std.int(oldPos / oldTabSize) + 1) * oldTabSize;
				else                                { newLine = oldLine.substr(j); break; }
				j++;
			}
			
			var oldIndents = Std.int(oldPos / oldIndentSize);
			var oldIndentAdditionalSpaces = oldPos % oldIndentSize;
			
			oldPos = oldIndents * newIndentSize + oldIndentAdditionalSpaces;
			
			var newPos = -shiftSize;
			var spaces = "";
			while (newTabSize > 0 && newPos + newTabSize <= oldPos) { newPos += newTabSize; spaces += "\t"; }
			while (newPos < oldPos) { newPos++; spaces += " "; }
			
			lines[i] = spaces + newLine;
		}
		
		var newText = lines.join("\n");
		if (newText != oldText)
		{
			File.saveContent(path, newText);
			if (verbose) log.trace("Fixed: " + path);
		}
	}
}