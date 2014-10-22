import stdlib.Regex;

class RefactorConvert extends RefactorReplace
{
	public function convert(filter:String, changeFileName:Regex, regexs:Array<Regex>, excludeStrings:Bool, excludeComments:Bool)
	{
		if (new Rules(verbose, log, [changeFileName].concat(regexs)).check())
		{
			replaceInFiles(new EReg(filter, "i"), changeFileName, regexs, excludeStrings, excludeComments);
		}
	}
	
	public function convertFile(inpFilePath:String, regexs:Array<Regex>, outFilePath:String, excludeStrings:Bool, excludeComments:Bool)
	{
		if (new Rules(verbose, log, regexs).check())
		{
			replaceInFile(inpFilePath, regexs, outFilePath, excludeStrings, excludeComments);
		}
	}
}