import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
using StringTools;

class RefactorPackage extends Refactor
{
	public function fixPackage(filter:EReg)
	{
		for (baseDir in baseDirs)
		{
			Log.start("Fix package in '" + baseDir + "'");
			
			FileSystemTools.findFiles(baseDir, function(path)
			{
				var localPath = path.substr(baseDir.length + 1);
				if (filter.match(localPath))
				{
					Log.start("Fix package in '" + path + "'");
					new TextFile(path, path, 2).process(function(text, _)
					{
						text = ~/^\s*package\s+[^;]+;\s*/.replace(text, "");
						return "package " + Path.directory(localPath).replace("/\\", ".") + ";\n\n" + text;
					});
					Log.finishSuccess();
				}
			});
			
			Log.finishSuccess();
		}
	}
}