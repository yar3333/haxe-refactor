
using StringTools;

class ClassPath
{
	public var full(default, null) : String;
	public var pack(default, null) : String;
	public var name(default, null) : String;
	
	public function new(full:String)
	{
		this.full = full;
		var n = full.lastIndexOf(".");
		pack = n < 0 ? "" : full.substr(0, n);
		name = n < 0 ? full : full.substr(n + 1);
	}
	
	public function getFilePath()
	{
		return StringTools.replace(full, ".", "/") + ".hx";
	}
	
	public static function fromFilePath(baseDir:String, filePath:String) : ClassPath
	{
		var local = filePath.substr(baseDir.length + 1, filePath.length - baseDir.length - 1 - ".hx".length);
		return new ClassPath(local.replace("/", "."));
	}
}
