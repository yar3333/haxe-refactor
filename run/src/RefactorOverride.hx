import hant.FileSystemTools;
import hant.Log;
import hant.Path;
import sys.FileSystem;
import sys.io.File;
using StringTools;
using Lambda;

typedef HaxeType =
{
	var baseDir : String;
	var file : TextFile;
 	var kind : String; // class | interface
	var name : String;
	var base : String;
	var interfaces : Array<String>;
	var processed : Bool;
}

class RefactorOverride extends Refactor
{
	var types = new Map<String, HaxeType>();
	
	public function overrideInFiles()
	{
		for (baseDir in baseDirs)
		{
			FileSystemTools.findFiles(baseDir, function(path)
			{
				if (path.endsWith(".hx"))
				{
					var type = readType(baseDir, path);
					if (type != null) types.set(path, type);
				}
			});
		}
		
		Log.start("Fix overrides");
		for (type in types)
		{
			overrideInType(type);
		}
		Log.finishSuccess();
	}
	
	function overrideInType(type:HaxeType)
	{
		if (type == null ||  type.processed) return;
		
		type.processed = true;
		
		for (baseTypeName in [type.base].concat(type.interfaces))
		{
			var baseTypeFilePath = findClassFilePath(type, baseTypeName);
			if (baseTypeFilePath != null)
			{
				overrideInType(types.get(baseTypeFilePath));
			}
		}
		
		if (verboseLevel > 1) Log.start("Process file '" + type.file.inpPath + "'; type " + type.name + "; base " + type.base + "; iterfaces " + type.interfaces);
		type.file.process(function(text, _)
		{
			text = processVars(text, type);
			text = processMethods(text, type);
			return text;
		});
		if (verboseLevel > 1) Log.finishSuccess();
	}
	
	function processVars(text:String, type:HaxeType) : String
	{
		return new EReg("(\n[ \t]*)(var\\s+)(" + Regexs.ID + ")([^;]*)", "g").map(text, function(re)
		{
			var varIndent = re.matched(1);
			var varPrefix = re.matched(2);
			var varName = re.matched(3);
			var varTail = re.matched(4);
			
			var reBase = new EReg("\n\\s*var\\s*(" + varName + "\\b[^;]*)", "");
			var baseType = getMatchedBaseType(type, reBase);
			if (baseType != null)
			{
				var baseVarNameAndTail = reBase.matched(1);
				
				if (baseType.kind == "class")
				{
					if (verboseLevel > 2) Log.echo("Variable " + baseType + "." + varName + " is redefined in " + type.name);
					return varIndent + "//" + varPrefix + varName + varTail;
				}
				else
				{
					var sig = (varName + varTail).replace(" ", "");
					var baseSig = baseVarNameAndTail.replace(" ", "");
					if (sig != baseSig)
					{
						if (verboseLevel > 2) Log.echo("Variable " + baseType + "." + varName + " is defined with different type in " + type.name);
						return varIndent + "//" + varPrefix + varName + varTail 
							 + "\n" + varIndent + "var " + baseVarNameAndTail;
					}
				}
			}
			return re.matched(0);
		});
	}
	
	function processMethods(text:String, type:HaxeType) : String
	{
		return new EReg(Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", Regexs.ID), "gs").map(text, function(re)
		{
			var overloads = splitOverloads(re.matched(1));
			var indentSpaces = re.matched(2);
			var funcName = re.matched(3);
			var funcTail = re.matched(4);
			
			if (funcName != "new")
			{
				var reBase = new EReg(Regexs.FULL_FUNC_DECL_TEMPLATE.replace("{ID}", funcName), "s");
				var baseType = getMatchedBaseType(type, reBase);
				if (baseType != null)
				{
					var baseFuncName = reBase.matched(3);
					var baseFuncTail = reBase.matched(4);
					
					if (verboseLevel > 2) Log.echo("Method " + baseType.name + "." + baseFuncName + " is overriden by " + type.name + "." + funcName);
					
					var newOverload = ("@:overload(function" + funcTail + "{})").replace(" ", "");
					if (overloads.indexOf(newOverload) < 0) overloads.push(newOverload);
					
					var baseAsOverload = ("@:overload(function" + baseFuncTail + "{})").replace(" ", "");
					overloads.remove(baseAsOverload);
					
					var resLines = overloads.concat([ (baseType.kind == "class" ? "override " : "") + "function " + baseFuncName + baseFuncTail ]);
					
					return resLines.map(function(s) return indentSpaces + s).join("\n");
				}
			}
			return re.matched(0);
		});
	}
	
	function splitOverloads(overloads:String) : Array<String>
	{
		return overloads.split("\n").map(function(s) return s.trim().replace(" ", "")).filter(function(s) return s != "");
	}
	
	function readType(baseDir:String, path:String) : HaxeType
	{
		if (path == null || path == "") return null;
		
		var file = new TextFile(path, path, true);
		var reExtends = "(?:\\s+extends\\s+" + Regexs.ID + ")?";
		var reImplement = "(?:\\s+implements\\s+" + Regexs.ID + ")*";
		var reClass = new EReg("\\b(class|interface)\\s+(" + Regexs.ID + ")(" + reExtends + ")(" + reImplement + ")[ \t\n]*\\{", "");
		if (reClass.match(file.text))
		{
			return
			{
				baseDir: baseDir,
				file: file,
				kind: reClass.matched(1),
				name: reClass.matched(2),
				base: reClass.matched(3).ltrim().substr("extends".length).trim(),
				interfaces: reClass.matched(4).split("implements").map(function(s) return s.trim()).filter(function(s) return s != ""),
				processed: false
			};
		}
		
		return null;
	}
	
	function getMatchedBaseType(type:HaxeType, re:EReg) : HaxeType
	{
		if (type == null) return null;
		
		for (baseTypeName in [ type.base ].concat(type.interfaces))
		{
			var baseTypeFilePath = findClassFilePath(type, baseTypeName);
			if (baseTypeFilePath != null)
			{
				var baseType = types.get(baseTypeFilePath);
				if (baseType != null && re.match(baseType.file.text)) return baseType;
			}
		}
		for (baseTypeName in [ type.base ].concat(type.interfaces))
		{
			var baseTypeFilePath = findClassFilePath(type, baseTypeName);
			if (baseTypeFilePath != null)
			{
				var r = getMatchedBaseType(types.get(baseTypeFilePath), re);
				if (r != null) return r;
			}
		}
		
		return null;
	}
	
	function findClassFilePath(refType:HaxeType, typeName:String) : String
	{
		if (typeName == null || typeName == "") return null;
		
		return findFile
		(
			typeName.indexOf(".") >= 0
				? typeName.replace(".", "/") + ".hx"
				: Path.directory(refType.file.inpPath.substr(refType.baseDir.length + 1)) + "/" + typeName + ".hx"
		);
	}
	
	function findFile(localPath:String) : String
	{
		for (i in 1...baseDirs.length + 1)
		{
			var baseDir = baseDirs[baseDirs.length - i];
			var path = Path.join([ baseDir, localPath ]);
			if (FileSystem.exists(path)) return path;
		}
		return null;
	}
}