import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
using StringTools;

class Refactor
{
	var baseDirs : Array<String>;
	var outDir : String;
	var verbose : Bool;
	
	public function new(baseDir:String, outDir:String, verbose:Bool)
	{
		this.outDir = outDir != null && outDir != "" ? Path.addTrailingSlash(outDir) : outDir;
		this.verbose = verbose;
		
		if (baseDir != null) baseDirs = DirTools.parse(baseDir, verbose);
	}
}