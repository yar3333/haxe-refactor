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
	
}