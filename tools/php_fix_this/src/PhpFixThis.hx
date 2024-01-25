import haxe.io.Path;
import stdlib.Regex;
using StringTools;

enum LexemType
{
	COMMENT;
	STRING;
	ARROW;
	BEG_FIGURE_BRACKET;
	END_FIGURE_BRACKET;
	ID;
	OTHER;
}

class Lexem
{
	public var type:LexemType;
	public var text:String;
	
	public function new(type:LexemType, text:String)
	{
		this.type = type;
		this.text = text;
	}
}

class PhpFixThis
{
    public static function fix(text: String) : String
	{
		var lexems = parse(text);
		
		
		
		
		return lexems.map(function(lexem) return lexem.text).join("");
	}
	
    public static function parse(text: String) : Array<Lexem>
    {
		var lexems = new Array<Lexem>();
		
		var reID = ~/^[$]?[_a-zA-Z][_a-zA-Z0-9]*/;
		
		while (text.length > 0)
		{
			if (text.startsWith("/*"))
			{
				var end = text.indexOf("*/");
				lexems.push(new Lexem(LexemType.COMMENT, text.substr(0, end + 2)));
				text = text.substr(end + 2);
			}
			else if (text.startsWith("//"))
			{
				var end = text.indexOf("\n");
				lexems.push(new Lexem(LexemType.COMMENT, text.substr(0, end + 1)));
				text = text.substr(end + 1);
			}
			else if (text.startsWith("'"))
			{
				var s = text.substr(0, 1); text = text.substr(1);
				while (text.length > 0)
				{
					if (text.startsWith("\\"))
					{
						s += text.substr(0, 1); text = text.substr(1);
						if (text.startsWith("u"))
						{
							s += text.substr(0, 5); text = text.substr(5);
						}
						else
						{
							s += text.substr(0, 1); text = text.substr(1);
						}
					}
					else
					if (text.startsWith("'"))
					{
						s += text.substr(0, 1); text = text.substr(1);
						break;
					}
					else
					{
						s += text.substr(0, 1); text = text.substr(1);
					}
				}
				lexems.push(new Lexem(LexemType.STRING, s));
			}
			else if (text.startsWith('"'))
			{
				var s = text.substr(0, 1); text = text.substr(1);
				while (text.length > 0)
				{
					if (text.startsWith("\\"))
					{
						s += text.substr(0, 1); text = text.substr(1);
						if (text.startsWith("u"))
						{
							s += text.substr(0, 5); text = text.substr(5);
						}
						else
						{
							s += text.substr(0, 1); text = text.substr(1);
						}
					}
					else
					if (text.startsWith('"'))
					{
						s += text.substr(0, 1); text = text.substr(1);
						break;
					}
					else
					{
						s += text.substr(0, 1); text = text.substr(1);
					}
				}
				lexems.push(new Lexem(LexemType.STRING, s));
			}
			else if (text.startsWith("->"))
			{
				lexems.push(new Lexem(LexemType.ARROW, text.substr(0, 2)));
				text = text.substr(2);
			}
			else if (text.startsWith("{"))
			{
				lexems.push(new Lexem(LexemType.BEG_FIGURE_BRACKET, text.substr(0, 1)));
				text = text.substr(1);
			}
			else if (text.startsWith("}"))
			{
				lexems.push(new Lexem(LexemType.END_FIGURE_BRACKET, text.substr(0, 1)));
				text = text.substr(1);
			}
			else if (reID.match(text))
			{
				lexems.push(new Lexem(LexemType.ID, reID.matched(0)));
				text = text.substr(reID.matched(0).length);
			}
			else
			{
				lexems.push(new Lexem(LexemType.OTHER, text.substr(0, 1)));
				text = text.substr(1);
			}
		}
		
		return lexems;
    }
}
