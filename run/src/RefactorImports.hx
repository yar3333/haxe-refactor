import sys.io.File;
import stdlib.Regex;
import hant.Process;
import hant.FileSystemTools;
import hant.Log;
import haxe.io.Path;
using StringTools;

class RefactorImports extends Refactor
{
	public function fixImports(hxmlFile:String, filter:EReg) : Void
	{
        Log.instance = new Log(10, 1); // force detail logging

        Log.start("Fix imports");

        Log.start("Collect data about types");
        var types = new Map<String, String>();
		for (baseDir in baseDirs)
		{
			FileSystemTools.findFiles(baseDir, path ->
			{
				var localPath = path.substr(baseDir.length + 1);
                if (!filter.match(localPath)) return;
                var shortName = Path.withoutDirectory(Path.withoutExtension(localPath));
                var fullName = Path.withoutExtension(localPath).replace("/", ".");
                Log.echo(shortName + " => " + fullName);
                types.set(shortName, fullName);
            });
        }
        Log.finishSuccess();

        var success = false;

        var replacer = new RefactorReplace(null, null);

        Log.start("Compile and fix loop");
        while (true)
        {
            Log.start("Compile");
                var r = Process.run("haxe", [ hxmlFile ], null, false, false);
                // library/js/three/textures/Texture.hx:176: characters 15-22 : Type not found : Vector2
                var re = ~/^([^:]+)[:]\d+: characters \d+-\d+ : Type not found : ([A-Z][_a-zA-Z0-9]*)\n/;
                if (r.exitCode == 0) { success = true; break; }
                if (!re.match(r.error)) { Log.finishFail("Unexpected compile error: " + r.error);  break; }
            Log.finishSuccess();

            var fileToAddImport = re.matched(1);
            var typeName = re.matched(2);
            
            Log.start("Fix `" + typeName + "` in " + fileToAddImport);
                if (!types.exists(typeName)) { Log.finishFail("Type `" + typeName + "` not found in specified folders."); break; }
                var fileBefore = File.getContent(fileToAddImport);
                replacer.replaceInFile(fileToAddImport, [ new Regex("/^\\s*package[^;]*;\\s*?\\n(?:\\s*import\\s+[^;]+\\s*;\\s*?\\n)*/$0import " + types.get(typeName) + ";\\n/s") ], fileToAddImport, false, false, 2);
                replacer.replaceInFile(fileToAddImport, [ new Regex("/^(\\s*package[^;]*;[ \t]*\\n)import/$0\\nimport") ], fileToAddImport, false, false, 2);
                var fileAfter = File.getContent(fileToAddImport);
                if (fileBefore == fileAfter) { Log.finishFail("Could't detect place to add import (possible, 'package' statememnt is not found)."); break; }
            Log.finishSuccess();
        }
        if (success) Log.finishSuccess();
        else         Log.finishFail();

        if (success) Log.finishSuccess();
        else         Log.finishFail();
	}
}