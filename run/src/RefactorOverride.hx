import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;

typedef ClassInfo =
{
	var name : String;
	var base : String;
	var interfaces : Array<String>;
}

class RefactorOverride extends Refactor
{
	var filesProcessed = new Map<String, Bool>();
	
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
		if (filesProcessed.exists(path)) return;
		
		filesProcessed.set(path, true);
		
		var localPath = path.substr(baseDir.length + 1);
		new TextFile(fs, path, path, verbose, log).process(function(text, fileApi)
		{
			var klass = getClassInfo(text);
			if (klass != null)
			{
				if (klass.base != "")
				{
					text = processMembers(text, localPath, klass.name, klass.base, true);
				}
				
				for (inter in klass.interfaces)
				{
					text = processMembers(text, localPath, klass.name, inter, false);
				}
			}
			
			return text;
		});
	}
	
	function getClassInfo(text:String) : ClassInfo
	{
		var reExtends = "(?:\\s+extends\\s+" + Regexs.ID + ")?";
		var reImplement = "(?:\\s+implements\\s+" + Regexs.ID + ")*";
		var reClass = new EReg("\\bclass\\s+(" + Regexs.ID + ")(" + reExtends + ")(" + reImplement + ")", "");
		if (reClass.match(text))
		{
			return
			{
				name: reClass.matched(1),
				base: reClass.matched(2).ltrim().substr("extends".length).trim(),
				interfaces: reClass.matched(3).split("implements").map(function(s) return s.trim()).filter(function(s) return s != "")
			};
		}
		return null;
	}
	
	function processMembers(text:String, localPath:String, klass:String, baseKlassName:String, baseIsClass:Bool) : String
	{
		text = new EReg("(\n[ \t]*)(var\\s+)(" + Regexs.ID + ")([^;]*)", "g").map(text, function(re)
		{
			var varIndent = re.matched(1);
			var varPrefix = re.matched(2);
			var varName = re.matched(3);
			var varTail = re.matched(4);
			
			var reBase = new EReg("\n\\s*var\\s*(" + re.matched(3) + "\\b[^;]*)", "");
			var baseKlassWithDefine = getMatchedBase(localPath, baseKlassName, reBase);
			if (baseKlassWithDefine != null)
			{
				var baseVarNameAndTail = reBase.matched(1);
				
				if (baseIsClass)
				{
					log.trace("Found: var " + baseKlassWithDefine + "." + varName + " is redefined in " + klass);
					return varIndent + "//" + varPrefix + varName + varTail;
				}
				else
				{
					var sig = (varName + varTail).replace(" ", "");
					var baseSig = baseVarNameAndTail.replace(" ", "");
					if (sig != baseSig)
					{
						log.trace("Found: var " + baseKlassWithDefine + "." + varName + " is defined with different type in " + klass);
						return varIndent + "//" + varPrefix + varName + varTail 
							 + "\n" + varIndent + "var " + baseVarNameAndTail;
					}
				}
			}
			return re.matched(0);
		});
		
		//log.trace("Search for methods of " + klassName + "...");
		//log.trace("re = " + Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", Regexs.ID).replace("\n", "\\n"));
		text = new EReg(Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", Regexs.ID), "gs").map(text, function(re)
		{
			var overloads = splitOverloads(re.matched(1));
			var indentSpaces = re.matched(2);
			var funcName = re.matched(3);
			var funcTail = re.matched(4);
			
			if (funcName != "new")
			{
				//log.trace("Regex found func " + klassName + "." + funcName);
				//log.trace("overloads = \n" + overloads.join("\n"));
				
				var reBase = new EReg(Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", funcName), "s");
				var baseKlassWithDefine = getMatchedBase(localPath, baseKlassName, reBase);
				if (baseKlassWithDefine != null)
				{
					//var baseOverloads = splitOverloads(reBase.matched(1));
					//var baseIndentSpaces = reBase.matched(2);
					var baseFuncName = reBase.matched(3);
					var baseFuncTail = reBase.matched(4);
					
					log.trace("Found: method " + baseKlassWithDefine + "." + baseFuncName + " is overriden by " + klass + "." + funcName);
					
					var newOverload = ("@:overload(function" + funcTail + "{})").replace(" ", "");
					if (overloads.indexOf(newOverload) < 0) overloads.push(newOverload);
					
					var baseAsOverload = ("@:overload(function" + baseFuncTail + "{})").replace(" ", "");
					//log.trace("baseAsOverload = " + baseAsOverload);
					overloads.remove(baseAsOverload);
					
					var resLines = overloads.concat([ (baseIsClass ? "override " : "") + "function " + baseFuncName + baseFuncTail ]);
					
					return resLines.map(function(s) return indentSpaces + s).join("\n");
				}
			}
			return re.matched(0);
		});
		
		return text;
	}
	
	function splitOverloads(overloads:String) : Array<String>
	{
		return overloads.split("\n").map(function(s) return s.trim().replace(" ", "")).filter(function(s) return s != "");
	}
	
	function getMatchedBase(localPath:String, baseKlassName:String, re:EReg) : String
	{
		var baseKlassFile = baseKlassName.indexOf(".") >= 0
			? findFile(baseKlassName.replace(".", "/") + ".hx")
			: findFile(Path.join([ Path.directory(localPath), baseKlassName + ".hx" ]));
		
		if (baseKlassFile == null) return null;
		
		overrideInFile(baseKlassFile.baseDir, baseKlassFile.path);
		
		var r = null;
		new TextFile(fs, baseKlassFile.path, null, verbose, log).process(function(text, _)
		{
			if (re.match(text)) r = baseKlassName;
			else
			{
				var next = getClassInfo(text);
				if (next != null)
				{
					var localPath = baseKlassFile.path.substr(baseKlassFile.baseDir.length + 1);
					if (next.base != "") r = getMatchedBase(localPath, next.base, re);
					for (inter in next.interfaces)
					{
						if (r != null) break;
						r = getMatchedBase(localPath, inter, re);
					}
				}
			}
			return null;
		});
		return r;
	}
	
	function findFile(localPath:String) : { baseDir:String, path:String }
	{
		for (i in 1...baseDirs.length + 1)
		{
			var baseDir = baseDirs[baseDirs.length - i];
			var path = Path.join([ baseDir, localPath ]);
			if (FileSystem.exists(path)) return { baseDir:baseDir, path:path };
		}
		return null;
	}
}