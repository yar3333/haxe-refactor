export class TypePathTools
{
    static normalizeFullClassName(s:string) : string
    {
        var pp = s.split(".");
        return pp.slice(0, pp.length - 1).map(TypePathTools.decapitalize).concat([ TypePathTools.capitalize(pp[pp.length-1]) ]).join(".");
    }

    static capitalize(s:string) : string
    {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    static decapitalize(s:string) : string
    {
        return s.charAt(0).toLowerCase() + s.slice(1);
    }

    static makeFullClassPath(parts:Array<string>) : string
    {
        var s = "";
        for (var p of parts)
        {
            if (p !== null && p !== "" && s != "") s += ".";
            s += p;
        }
        return s;
    }

	static splitFullClassName(fullClassName:string) : { packageName:string, className:string }
	{
		var packageName = '';
		var className = fullClassName;
		
		if (fullClassName.lastIndexOf('.') != -1)
		{
			packageName = fullClassName.substr(0, fullClassName.lastIndexOf('.'));
			className = fullClassName.substr(fullClassName.lastIndexOf('.') + 1);
		}
		
		return { packageName:packageName, className:className };
	}
}