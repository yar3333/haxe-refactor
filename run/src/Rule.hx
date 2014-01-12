package ;

import hant.Log;
using StringTools;

class Rule
{
	public var search : String;
	public var replacement : String;
	public var flags : String;
	
	public function new(re:String)
	{
		re = re.trim();
		
		var delimiter = re.substr(0, 1);
		
		search = "";
		var i = 1; while (i < re.length)
		{
			var c = re.substr(i, 1);
			if (c == delimiter)
			{
				i++;
				break;
			}
			else
			if (c == "\\") 
			{
				i++;
				c = re.substr(i, 1);
				if (c == "r") search += "\r";
				else
				if (c == "n") search += "\n";
				else
				if (c == "t") search += "\t";
				else
				search += c;
			}
			else
			{
				search += c;
			}
			i++;
		}
		
		replacement = "";
		while (i < re.length)
		{
			var c = re.substr(i, 1);
			if (c == delimiter)
			{
				i++;
				break;
			}
			else
			if (c == "\\") 
			{
				i++;
				c = re.substr(i, 1);
				if (c == "r") replacement += "\r";
				else
				if (c == "n") replacement += "\n";
				else
				if (c == "t") replacement += "\t";
				else
				replacement += c;
			}
			else
			{
				replacement += c;
			}
			i++;
		}
		
		flags = re.substr(i);
	}
	
	public function apply(text:String, ?log:Log) : String
	{
		if (replacement == "$-") replacement = "";
		
		var counter = 0;
		
		var r = new EReg(search, "g" + flags.replace("g", "")).map(text, function(re)
		{
			var s = "";
			var i = 0;
			while (i < replacement.length)
			{
				var c = replacement.charAt(i++);
				if (c != "$")
				{
					s += c;
				}
				else
				{
					c = replacement.charAt(i++);
					if (c == "$")
					{
						s += "$";
					}
					else
					{
						var command = "";
						if ("0123456789".indexOf(c) < 0)
						{
							command = c;
							c = replacement.charAt(i++);
						}
						var number = Std.parseInt(c);
						var t = re.matched(number);
						switch(command)
						{
							case "^": t = t.toUpperCase();
							case "v": t = t.toLowerCase();
						}
						s += t;
					}
				}
			}
			
			if (log != null) log.trace(re.matched(0).replace("\r", "").replace("\n", "\\n") + " => " + s);
			
			return s;
		});
		
		return r;
		
	}
}