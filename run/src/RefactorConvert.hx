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
}