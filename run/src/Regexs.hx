class Regexs
{
	static inline var OVERLOAD = "[ \t]*@[:]overload[^\n]+";
	static inline var FUNC_PREFIX = "(?:(?:public|private)\\s+)?(?:override\\s+)?";
	
	public static inline var ID = "[_a-zA-Z][_a-zA-Z0-9]*";
	
	public static inline var TYPE = ID + "(?:[<]\\s*" + ID + "(?:\\s*,\\s*" + ID + ")*\\s*[>])?";
	
	public static inline var OVERLOADS = "(?:" + OVERLOAD + "[\n]+)*";
	
	//                                                           1                   2                                 3=func name        4=(params):ret
	public static inline var FULL_FUNC_DECL_TEMPLATE = "(" + OVERLOADS + ")(?<=\n)([ \t]*)" + FUNC_PREFIX + "function\\s+({ID})(\\s*\\([^)]*\\)\\s*[:]\\s*" + TYPE + ")";
	
	public static inline var UNNECESSARY_SPACES = "([^a-zA-Z0-9]|^)\\s+([^a-zA-Z0-9]|$)";
}