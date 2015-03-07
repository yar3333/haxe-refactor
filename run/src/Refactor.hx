import hant.Path;

class Refactor
{
	var baseDirs : Array<String>;
	var outDir : String;
	
	public function new(baseDir:String, outDir:String)
	{
		this.outDir = outDir != null && outDir != "" ? Path.addTrailingSlash(outDir) : outDir;
		
		if (baseDir != null) baseDirs = DirTools.parse(baseDir);
	}
}