import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

class RefactorOverride extends Refactor
{
	public function overrideInFiles()
	{
		for (baseDir in baseDirs)
		{
			log.start("Fix overrides in '" + baseDir + "'");
			
			fs.findFiles(baseDir, function(path)
			{
				if (path.endsWith(".hx"))
				{
					overrideInFile(baseDir, path);
				}
			});
			
			log.finishOk();
		}
	}
	
	function overrideInFile(baseDir:String, path:String)
	{
		var localPath = path.substr(baseDir.length + 1);
		new TextFile(fs, path, path, verbose, log).process(function(text, fileApi)
		{
			var reBaseClass = new EReg("class\\s+(" + Regexs.ID + ")\\s+extends\\s+(" + Regexs.ID + ")", "s");
			if (reBaseClass.match(text))
			{
				var klassName = reBaseClass.matched(1);
				var baseKlassName = reBaseClass.matched(2);
				
				if (Path.withoutExtension(Path.withoutDirectory(path)) == klassName)
				{
					text = new EReg("(\n[ \t]*)(var\\s+)(" + Regexs.ID + ")", "g").map(text, function(re)
					{
						var reBase = new EReg("\n\\s*var\\s*" + re.matched(3), "");
						if (isMatchClass(localPath, baseKlassName, reBase))
						{
							return re.matched(1) + "//" + re.matched(2) + re.matched(3);
						}
						return re.matched(0);
					});
					
					log.trace("Search for methods of " + klassName + "...");
					log.trace("re = " + Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", Regexs.ID).replace("\n", "\\n"));
					text = new EReg(Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", Regexs.ID), "gs").map(text, function(re)
					{
						var overloads = re.matched(1).split("\n").map(function(s) return s.trim().replace(" ", "")).filter(function(s) return s != "");
						var indentSpaces = re.matched(2);
						var funcName = re.matched(3);
						var funcTail = re.matched(4);
						
						log.trace("Regex found func " + klassName + "." + funcName);
						log.trace("overloads = \n" + overloads.join("\n"));
						
						var reBase = new EReg(Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", funcName), "s");
						if (isMatchClass(localPath, baseKlassName, reBase))
						{
							var baseOverloads = reBase.matched(1).split("\n").map(function(s) return s.trim().replace(" ", "")).filter(function(s) return s != "");
							var baseIndentSpaces = reBase.matched(2);
							var baseFuncName = reBase.matched(3);
							var baseFuncTail = reBase.matched(4);
							
							log.trace("Found " + baseKlassName + "." + baseFuncName + " is overriden by " + klassName + "." + funcName);
							
							var newOverload = ("@:overload(function" + funcTail + "{})").replace(" ", "");
							if (overloads.indexOf(newOverload) < 0) overloads.push(newOverload);
							
							var baseAsOverload = ("@:overload(function" + baseFuncTail + "{})").replace(" ", "");
							trace("baseAsOverload = " + baseAsOverload);
							overloads.remove(baseAsOverload);
							
							var resLines = overloads.concat([ "override function " + baseFuncName + baseFuncTail ]);
							
							return resLines.map(function(s) return indentSpaces + s).join("\n");
						}
						return re.matched(0);
					});
				}
			}
			
			return text;
		});
	}
	
	function isMatchClass(localPath:String, baseKlassName:String, re:EReg) : Bool
	{
		var baseKlassFilePath = baseKlassName.indexOf(".") >= 0
			? findFile(baseKlassName.replace(".", "/") + ".hx")
			: findFile(Path.join([ Path.directory(localPath), baseKlassName+".hx" ]));
		log.trace("isMatchClass: baseKlassFilePath = " + baseKlassFilePath);
		return baseKlassFilePath != null && re.match(File.getContent(baseKlassFilePath));
	}
	
	function findFile(localPath:String) : String
	{
		for (i in 1...baseDirs.length + 1)
		{
			var filePath = Path.join([ baseDirs[baseDirs.length - i], localPath ]);
			if (FileSystem.exists(filePath)) return filePath;
		}
		return null;
	}
}