import stdlib.Regex;

class RefactorConvert extends RefactorReplace
{
	public function convert(filter:String, changeFileName:Regex, regexs:Array<Regex>, excludeStrings:Bool, excludeComments:Bool)
	{
		if (new Rules([changeFileName].concat(regexs), verbose).check())
		{
			replaceInFiles(new EReg(filter, "i"), changeFileName, regexs, excludeStrings, excludeComments);
		}
	}
	
	public function convertFile(inpFilePath:String, regexs:Array<Regex>, outFilePath:String, excludeStrings:Bool, excludeComments:Bool)
	{
		if (new Rules(regexs, verbose).check())
		{
			replaceInFile(inpFilePath, regexs, outFilePath, excludeStrings, excludeComments);
		}
	}
	
	public function convertText(text:String, regexs:Array<Regex>, excludeStrings:Bool, excludeComments:Bool)
	{
		return replaceInText(text, regexs, excludeStrings, excludeComments);
	}
}