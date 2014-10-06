import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
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
		
		if (baseDir != null) baseDirs = DirTools.parse(baseDir, log, verbose);
	}
}