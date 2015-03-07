import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
using StringTools;

class Refactor
{
	var baseDirs : Array<String>;
	var outDir : String;
	var verboseLevel : Int;
	
	public function new(baseDir:String, outDir:String, verboseLevel:Int)
	{
		this.outDir = outDir != null && outDir != "" ? Path.addTrailingSlash(outDir) : outDir;
		this.verboseLevel = verboseLevel;
		
		if (baseDir != null) baseDirs = DirTools.parse(baseDir, verboseLevel > 0);
	}
}