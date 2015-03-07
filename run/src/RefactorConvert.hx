import stdlib.Regex;

class RefactorConvert extends RefactorReplace
{
	public function convert(filter:String, changeFileName:Regex, regexs:Array<Regex>, excludeStrings:Bool, excludeComments:Bool, baseLogLevel:Int)
	{
		if (new Rules([changeFileName].concat(regexs)).check())
		{
			replaceInFiles(new EReg(filter, "i"), changeFileName, regexs, excludeStrings, excludeComments, baseLogLevel);
		}
	}
	
	public function convertFile(inpFilePath:String, regexs:Array<Regex>, outFilePath:String, excludeStrings:Bool, excludeComments:Bool, baseLogLevel:Int)
	{
		if (new Rules(regexs).check())
		{
			replaceInFile(inpFilePath, regexs, outFilePath, excludeStrings, excludeComments, baseLogLevel);
		}
	}
	
	public function convertText(text:String, regexs:Array<Regex>, excludeStrings:Bool, excludeComments:Bool) : String
	{
		return replaceInText(text, regexs, excludeStrings, excludeComments, 1000);
	}
}