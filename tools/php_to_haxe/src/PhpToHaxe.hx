import php.MathNatives.base_convert;
import php.PcreNatives;
import php.PcreNatives.preg_match_all_ex;
import php.PcreNatives.preg_match_ex;
import php.PcreNatives.preg_replace;
import php.TokenizerNatives.token_get_all;
import php.TokenizerNatives.token_name;
import php.Tokens;
import php.TypedArray;
import php.VarNatives.is_array;
import php.VarNatives.is_string;
import php.VarNatives.isset;
import stdlib.Debug;
import sys.io.File;
using stdlib.StringTools;
using stdlib.Lambda;

class PhpToHaxe
{
    var typeNamesMapping : Map<String, String>;
    var functionNameMapping : Map<String, Dynamic>; // => string | array<string|int>
    var varNamesMapping : Map<String, String>;
    var wantExtern : Bool;
    var reservedWords : Array<String>;
	var magickFunctionNameMapping:Map<String, String>;
    
    public function new(typeNamesMapping: Map<String, String>, varNamesMapping:Map<String, String>, functionNameMapping:Map<String, Dynamic>, magickFunctionNameMapping:Map<String, String>, reservedWords:Array<String>, wantExtern=false) : Void
    {
        this.typeNamesMapping = typeNamesMapping;
        this.varNamesMapping = varNamesMapping;
        this.functionNameMapping = functionNameMapping;
        this.magickFunctionNameMapping = magickFunctionNameMapping;
        this.wantExtern = wantExtern;
        this.reservedWords = reservedWords;
    }
	
    public function getHaxeCode(text:String) : String
    {
        text = text.replace("\r\n", "\n");
        text = text.replace("\r", "\n");
        text = ~/^(\s*<[?]php)+/.replace(text, "");
        var tokens = token_get_all("<?php " + text);
        
		//for ($i =0; $i<count($tokens); $i++) { $t = $tokens[$i]; echo (is_array($t) ? token_name($t[0]) . " => " . $t[1] . " / " . $t[2] : $t) . "\n"; }
		
        var names = new Array<String>();
        var values = new Array<String>();
        for (token in tokens)
        {
            if (is_array(token))
            {
                names.push(token_name(token[0]));
                values.push(token[1]);
            }
            else
            {
                names.push(cast token);
                values.push(cast token);
            }
        }
		
        if (names.length>0 && names[0]=='T_OPEN_TAG')
        {
            names.shift();
            values.shift();
        }
		
		processBasicValues(names, values);
		processVarsInStrings(names, values);
        changeProtectedToPrivate(names, values);
        changeStdValuesToLowerCase(names, values);
        changeOctalNumberToHex(names, values);
        changeReservedWords(names, values);
        changeIsIdenticalToIsEqual(names, values);
		
		var r = tokensToText(names, values);
        if (wantExtern) r = ~/[\t ]*\n[\t ]*\n[\t ]*\n/g.replace(r, "\n\n");
        return r;
    }
	
	function processBasicValues(names:Array<String>, values:Array<String>) : Void
	{
		var i = 0; while (i < names.length)
		{
			if (names[i] == "T_STRING")
			{
				switch (values[i].toLowerCase())
				{
					case "true": values[i] = "true";
					case "false": values[i] = "false";
					case "null": values[i] = "null";
				}
			}
			i++;
		}
	}
	
	function processVarsInStrings(names:Array<String>, values:Array<String>) : Void
	{
		var i = 0; while (i < names.length)
		{
			switch (names[i])
			{
				case "'":
					names[i] = '"';
					values[i] = '"';
					
				case '"':
					names[i] = "'";
					values[i] = "'";
					
				case "T_DOLLAR_OPEN_CURLY_BRACES":
					names[i] = "T_ENCAPSED_AND_WHITESPACE";
					var end = findLexemPosOnCurrentLevel(names, i, "}");
					names[end] = "T_ENCAPSED_AND_WHITESPACE";
					i = end;
					
				case "T_CURLY_OPEN":
					names[i] = "T_ENCAPSED_AND_WHITESPACE";
					values[i] = "${";
					var end = findLexemPosOnCurrentLevel(names, i, "}");
					names[end] = "T_ENCAPSED_AND_WHITESPACE";
					i = end;
			}
			i++;
		}
	}
	
    private function changeProtectedToPrivate(names:Array<String>, values:Array<String>) : Void
    {
        for (i in 0...names.length)
        {
            if (names[i]=='T_PROTECTED')
            {
                names[i] = 'T_PRIVATE';
                values[i] = 'private';
            }
        }
    }
    
    private function changeStdValuesToLowerCase(names:Array<String>, values:Array<String>) : Void
    {
        for (i in 0...names.length)
        {
            if (names[i]=='T_STRING')
            {
				var lc = values[i].toLowerCase();
				switch (lc)
				{
					case "true":
					case "false":
					case "null":
						values[i] = lc; 
				}
            }
        }
    }
    
    private function changeOctalNumberToHex(names:Array<String>, values:Array<String>) : Void
    {
        for (i in 0...names.length)
        {
            if (names[i]=='T_LNUMBER')
            {
				var s = values[i];
				if (s.charAt(0)=="0" && s.length>1)
				{
					values[i] = "/*" + s + "*/0x" + base_convert(s, 8, 16);
				}
            }
        }
    }
    
    private function changeReservedWords(names:Array<String>, values:Array<String>) : Void
    {
        for (i in 0...names.length)
        {
            if (names[i]=='T_VARIABLE')
            {
				var s = values[i];
				if (this.reservedWords.indexOf(s) >= 0)
				{
					values[i] = s+ "_";
				}
            }
        }
    }
    
    private function changeIsIdenticalToIsEqual(names:Array<String>, values:Array<String>) : Void
    {
        for (i in 0...names.length)
        {
            if (names[i]=='T_IS_IDENTICAL')
            {
				values[i] = '==';
            }
        }
    }
    
    private function isBeforeLexem(names:Array<String>, n:Int, lexems:Array<String>, dist:Int) : Bool
    {
		var i = n + 1;
		while (dist > 0 && i < names.length)
		{
            if (names[i] == 'T_WHITESPACE' || names[i] == 'T_COMMENT') { i++; continue; }
            if (lexems.has(names[i])) return true;
            dist--;
			
			i++;
        }
        return false;
    }
	
    private function isAfterLexem(names:Array<String>, n:Int, lexems:Array<String>, dist:Int) : Bool
    {
		var i = n - 1;
		while (dist > 0 && i >= 0)
        {
            if (names[i] == 'T_WHITESPACE' || names[i] == 'T_COMMENT') { i--;  continue; }
            if (lexems.has(names[i])) return true;
            dist--;
			i--;
        }
        return false;
    }
	
    private function getPairPos(names:Array<String>, i:Int) : Int
    {
        var stack = [];
        while (i<names.length)
        {
            if ([ '(','{','[' ].has(names[i]))
            {
                stack.push(names[i]);
            }
            else
            if ([ ')','}',']' ].has(names[i]))
            {
                stack.pop();
                if (stack.length == 0) return i;
            }
            i++;
        }
        throw "Fatal error: pair not found.";
    }
	
    private function findLexemPosOnCurrentLevel(names:Array<String>, i:Int, lexem:String) : Int
    {
        var stack = [];
        while (i<names.length)
        {
            if (names[i]==lexem && stack.length==0) return i;
			
            if ([ '(', '{', '[' ].has(names[i])) stack.push(names[i]);
            if ([ ')', '}', ']' ].has(names[i])) stack.pop();
			
			i++;
        }
        throw "Fatal error: lexem '" + lexem + "' not found from position " + i + ".";
    }
	
	private function splitTokensByComma(names:Array<String>, values:Array<String>) : Array<{ names:Array<String>, values:Array<String> }>
    {
        var params = [];
		
        var param = { names:[], values:[] };
		
		var stack = [];
        for (i in 0...names.length)
        {
            if ([ '(','{','[' ].has(names[i])) stack.push(names[i]);
            if ([ ')','}',']' ].has(names[i])) stack.pop();
			
            if (names[i]==',' && stack.length==0)
            {
                params.push(param);
                param = { names:[], values:[] };
            }
            else
            {
                if (param.names.length > 0 || names[i] != 'T_WHITESPACE')
                {
                    param.names.push(names[i]);
                    param.values.push(values[i]);
                }
            }
        }
        if (param.names.length > 0) params.push(param);
		
        return params;
    }
	
    private function trimAndPad(names:Array<String>, values:Array<String>, padLeft:Int, padRight:Int) : Void
    {
        while (names.length>0 && names[0]=='T_WHITESPACE')
        {
            names.shift();
            values.shift();
        }
        while (names.length>0 && names[names.length-1]=='T_WHITESPACE')
        {
            names.pop();
            values.pop();
        }
        for (i in 0...padLeft)
        {
            names.unshift('T_WHITESPACE');
            values.unshift(' ');
        }
        for (i in 0...padRight)
        {
            names.push('T_WHITESPACE');
            values.push(' ');
        }
    }
	
    private function isSolidExpression(names:Array<String>) : Bool
    {
        var k = 0;
        for (name in names)
        {
            if (name=='T_DOUBLE_COLON' || name=='T_OBJECT_OPERATOR') k-=2;
            if (name!='T_WHITESPACE' && name!='T_COMMENT')
            {
                k++;
                if (k>1) return false;
            }
        }
        return true;
    }
	
    private function tokensToText(names:Array<String>, values:Array<String>) : String
    {
        var text = '';
        var i = 0; while (i<names.length)
        {
            switch (names[i])
            {
                case '.':
                    values[i] = '+';
					
                case 'T_CONCAT_EQUAL':
                    values[i] = '+=';
					
                case 'T_CLASS':
                    if (this.wantExtern)
                    {
                        values[i] = 'extern '+values[i];
                    }
					
                case 'T_DOUBLE_COLON':
                    if (i-1>=0 && names[i-1]=='T_STRING' && values[i-1]=='parent')
                    {
                        values[i-1] = 'super';
                    }
                    if (i-1>=0 && names[i-1]=='T_STRING' && values[i-1]=='self')
                    {
                        names[i-1] = 'T_COMMENT';
                        values[i-1] = '/*self.*/';
                        names[i] = 'T_WHITESPACE';
                        values[i] = '';
                    }
                    else
                    {
                        values[i] = '.';
                    }
					
                case 'T_OBJECT_OPERATOR':
                    if (i-1>=0 && names[i-1]=='T_STRING' && values[i-1]=='self')
                    {
                        names[i-1] = 'T_COMMENT';
                        values[i-1] = '/*self.*/';
                        names[i] = 'T_WHITESPACE';
                        values[i] = '';
                    }
                    else
                    {
                        values[i] = '.';
                    }
					
                case 'T_PRIVATE':
                    if (this.wantExtern)
                    {
                        if (this.isBeforeLexem(names, i, ['T_VARIABLE'], 2))
                        {
                            var beg = i - 1;
                            while (beg > 0 && [ 'T_STATIC', 'T_WHITESPACE', 'T_DOC_COMMENT' ].has(names[beg])) beg--;
                            beg++;
                            var end = this.findLexemPosOnCurrentLevel(names, i, ';');
                            names.splice(beg, end - beg + 1);
                            values.splice(beg, end - beg + 1);
                            i = beg - 1;
                        }
                    }
					
                case 'T_PUBLIC':
                    values[i] = 'public';
					
                case 'T_STATIC':
                    values[i] = 'static';
					
                case 'T_VARIABLE':
                    this.processVar(names, values, i);
					
                case 'T_STRING':
                    if (this.isAfterLexem(names, i, ['T_CONST'], 1))
                    {
                        var type = this.detectVarType(names, values, i);
                        if (type!='') values[i] = values[i] + " : " + type;
                    }
                    else
                    {
                        i = this.processFunctionCall(names, values, i);
                    }
                    
                case 'T_ARRAY':
                    if (i+1<names.length && names[i+1]=='(')
                    {
                        values[i] = '';
                        names[i+1] = '[';
                        values[i+1] = '[ ';
                        var n = this.getPairPos(names, i+1);
                        names[n] = ']';
                        values[n] = ' ]';
						
                        if (n-i==2)
                        {
                            values[i+1] = '[';
                            values[n] = ']';
                        }
                    }
					
                case 'T_FUNCTION':
                    i = this.processFunction(names, values, i);
                    
                case 'T_CONST':
                    values[i] = 'public static inline var';
                    
                case 'T_INCLUDE_ONCE', 'T_REQUIRE_ONCE':
                    values[i] = 'import'; i++;
                    while (names[i]=='T_WHITESPACE') i++;
                    values[i] = "/*" + values[i]; i++;
                    while (names[i]!=';') i++;
                    values[i] = "*/;";
                    
                case 'T_FOREACH':
                    values[i] = 'for';
					
                    var parBegin = this.findLexemPosOnCurrentLevel(names, i+1, '(');
					
                    var asPos = this.findLexemPosOnCurrentLevel(names, parBegin+1, 'T_AS');
                    names[asPos] = '__PROCESSED';
                    values[asPos] = 'in';
					
                    var namesList  = names.splice(parBegin+1, asPos - parBegin-1);
                    var valuesList = values.splice(parBegin+1, asPos - parBegin-1);
					this.trimAndPad(namesList, valuesList, 1, 0);
					
                    var parEnd = this.getPairPos(names, parBegin);
					
                    var namesVar  = names .spliceEx(parBegin+2, parEnd - parBegin-2, namesList);
                    var valuesVar = values.spliceEx(parBegin+2, parEnd - parBegin-2, valuesList);
                    this.trimAndPad(namesVar, valuesVar, 0, 1);
					
                    names .spliceEx(parBegin+1, 0, namesVar);
                    values.spliceEx(parBegin+1, 0, valuesVar);
            }
			
			i++;
        }
		
        return values.join("");
    }
    
    private function processFunction(names:Array<String>, values:Array<String>, i:Int) : Int
    {
        var phpEmptyArray = 'untyped __php__("array()")';
        
		if (this.wantExtern)
        {
            if (this.isAfterLexem(names, i, ['T_PRIVATE'], 2))
            {
                var begFunc = i-1;
                while (begFunc>0 && [ 'T_DOC_COMMENT', 'T_PRIVATE', 'T_STATIC', 'T_WHITESPACE' ].has(names[begFunc])) begFunc--;
                begFunc++;
                
                var endFunc = i+1;
                while (![ ';', '{' ].has(names[endFunc])) endFunc++;
                
                if (names[endFunc]=='{')
                {
                    endFunc = this.getPairPos(names, endFunc);
                    names.splice(begFunc, endFunc - begFunc + 1);
                    values.splice(begFunc, endFunc - begFunc + 1);
                    i = begFunc;
                    return i;
                }
            }
        }
		
        if (!this.isAfterLexem(names, i, [ 'T_PUBLIC', 'T_PRIVATE' ], 2))
        {
            values[i] = 'public ' + values[i];
        }
        
        var commentIndex = i - 1;
        while (commentIndex > 0 && [ 'T_WHITESPACE', 'T_PUBLIC', 'T_PRIVATE', 'T_STATIC' ].has(names[commentIndex])) commentIndex--; 
        
        var commentVarTypes = new TypedArray<String, String>();
        var returnType = 'void';
        if (commentIndex>=0 && names[commentIndex]=='T_DOC_COMMENT' )
        {
            commentVarTypes = this.getVarTypesByDocComment(values[commentIndex]);
            returnType = this.getReturnTypesByDocComment(values[commentIndex]);
            this.processDocComment(names, values, commentIndex);
        }
        
        var n = i + 1;
        while (names[n]=='T_WHITESPACE') n++;
        if (names[n]!='T_STRING') return i;
        
        if (this.magickFunctionNameMapping.exists(values[n]))
        {
			values[n] = this.magickFunctionNameMapping.get(values[n]);
        }
        
        var methodName = values[n];
        
        n++;
        while (names[n]=='T_WHITESPACE') n++;
        if (names[n]!='(') return i;
        var begParamsIndex = n;
        var endParamsIndex = this.getPairPos(names, n);
        
        var params = this.splitTokensByComma
		(
             names.slice(begParamsIndex + 1, endParamsIndex)
            ,values.slice(begParamsIndex + 1, endParamsIndex)
        );
        
        var resParamsStr = [];
		var vars = [];
        for (param in params)
        {
            var paramNames = param.names;
            var paramValues = param.values;
            this.trimAndPad(paramNames, paramValues, 0, 0);
            
            var type = '';
            var name = '';
            var defVal = '';
            
            if (paramNames.length > 1 && (paramNames[0] == 'T_STRING' || paramNames[0] == 'T_ARRAY'))
            {
                type = paramValues[0];
                paramNames.shift(); paramValues.shift();
                this.trimAndPad(paramNames, paramValues, 0, 0);
            }
            
			while (paramNames.length > 0 && paramNames[0] == "&") { paramNames.shift(); paramValues.shift(); }
			
            if (paramNames.length > 0 && paramNames[0] == 'T_VARIABLE')
            {
                name = paramValues[0].substr(1);
                paramNames.shift(); paramValues.shift();
                this.trimAndPad(paramNames, paramValues, 0, 0);
            }
            
            if (paramNames.length > 0 && paramNames[0] == '=')
            {
                paramNames.shift(); paramValues.shift();
                this.trimAndPad(paramNames, paramValues, 0, 0);
                if (paramNames.length > 0)
                {
                    defVal = paramValues[0];
                    paramNames.shift(); paramValues.shift();
                    this.trimAndPad(paramNames, paramValues, 0, 0);
                    if (paramNames.length>=2 && paramNames[0]=='(' && paramNames[1]==')')
                    {
                        defVal += "()";
                        paramNames.shift(); paramValues.shift();
                        paramNames.shift(); paramValues.shift();
                    }
                    else if (paramNames.length>=3 && paramNames[0]=='(' && paramNames[1]=='T_WHITESPACE' && paramNames[2]==')')
                    {
                        defVal += "()";
                        paramNames.shift(); paramValues.shift();
                        paramNames.shift(); paramValues.shift();
                        paramNames.shift(); paramValues.shift();
                    }
                        
                        
                    if (defVal=='array()') defVal = phpEmptyArray;
                }
                
            }
            
            if (type == '' && isset(commentVarTypes[name]))
            {
                type = commentVarTypes[name];
            }
            
            if (type=='')
            {
                if (defVal=='true' || defVal=='false') type = 'Bool';
                else
                if (defVal==phpEmptyArray) type = 'NativeArray';
            }
            
            resParamsStr.push(name + (type != '' ? ':' + this.getHaxeType(type):'') + (defVal != '' ? '=' + defVal : ''));
			vars.push(name);
        }
        
        names .spliceEx(begParamsIndex + 1, endParamsIndex - begParamsIndex - 1, ['T_COMMENT']);
        values.spliceEx(begParamsIndex + 1, endParamsIndex - begParamsIndex - 1, [resParamsStr.join(", ")]);
        
        i = begParamsIndex + 2;
		
        if (returnType!='')
        {
	        names .spliceEx(begParamsIndex + 3, 0, ['T_COMMENT']);
            values.spliceEx(begParamsIndex + 3, 0, [" : " + this.getHaxeType(returnType)]);
            i++;
        }
        
		var funcBeg = i + 1;
		while (names[funcBeg]=='T_WHITESPACE') funcBeg++;
		if (values[funcBeg] == '{')
		{
			var funcEnd = this.getPairPos(names, funcBeg);
			if (this.wantExtern)
			{
					names .spliceEx(i+1, funcEnd-i, [';']);
					values.spliceEx(i+1, funcEnd-i, [';']);
					i = funcBeg;
			}
			else
			{
				processFunctionBody(names, values, vars, funcBeg, funcEnd);
			}
		}
		
		return i;
    }
	
	function processFunctionBody(names:Array<String>, values:Array<String>, vars:Array<String>, funcBeg:Int, funcEnd:Int)
	{
		var i = funcBeg; while (i < funcEnd)
		{
			if (names[i] == "T_VARIABLE" && isAfterLexem(names, i, [";", "{", "}"], 10) && !vars.has(values[i].substr(1)))
			{
				var varNameIndex = i;
				i++;
				while (names[i] == "T_WHITESPACE") i++;
				if (values[i] == "=")
				{
					vars.push(values[varNameIndex].substr(1));
					values[varNameIndex] = "var " + values[varNameIndex];
				}
			}
			
			i++;
		}
	}
    
    function getVarTypesByDocComment(comment:String) : TypedArray<String, String>
    {
        var r = new TypedArray();
        
        var matches : TypedArray<Int, TypedArray<String, String>> = null;
		if (preg_match_all_ex("/@param\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)\\s+[\\$]?(?<name>[_a-zA-Z][_a-zA-Z0-9]*)/", comment, matches, PcreNatives.PREG_SET_ORDER) > 0)
        {
            for (m in matches)
            {
                r[m['name']] = m['type'];
            }
        }
        
        return r;
    }
    
    function getReturnTypesByDocComment(comment:String) : String
    {
		var m : TypedArray<String, TypedArray<Int, String>> = null;
        if (preg_match_all_ex("/@return\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)/", comment, m)>0)
        {
			return m['type'].join("|");
        }
        return '';
    }
    
    function getHaxeType(phpType:String) : String
    {
		if (wantExtern) return getHaxeTypeForExtern(phpType);
		else            return getHaxeTypeForCode(phpType);
		
    }
	
	function getHaxeTypeForExtern(phpType:String) : String
	{
		var n = phpType.indexOf("|");
		if (n <= 0) return getHaxeTypeForCode(phpType);
		return "EitherType<" + getHaxeTypeForCode(phpType.substring(0, n)) + ", " + getHaxeTypeForExtern(phpType.substring(n + 1)) + ">";
		
	}
    
	function getHaxeTypeForCode(phpType:String) : String
    {
		if (phpType.indexOf("|") >= 0) return "Dynamic";
		
		if (typeNamesMapping.exists(phpType.toLowerCase()))
        {
            return typeNamesMapping.get(phpType.toLowerCase());
        }
        return phpType;
	}
    
    private function processDocComment(names:Array<String>, values:Array<String>, i:Int) : Void
    {
        var comment = values[i];
        
        comment = PcreNatives.preg_replace("/(@param\\s+)[_a-zA-Z][_a-zA-Z0-9]*\\s+[\\$]([_a-zA-Z0-9]*)/", "\\1\\2", comment);
        comment = PcreNatives.preg_replace("/^\\s*[*]\\s*@param\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*[\r\n]+/m", "", comment);
        comment = PcreNatives.preg_replace("/^\\s*[*]\\s*@return\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*[\r\n]+/m", "", comment);
        comment = PcreNatives.preg_replace("/^\\s*[*]\\s*[\r\n]+/m", "", comment);
        
        values[i] = comment;
    }

    private function detectVarType(names:Array<String>, values:Array<String>, i:Int) : String
    {
        var n = i - 1;
        while (n > 0 && [ 'T_WHITESPACE','T_PUBLIC','T_PRIVATE','T_CONST','T_STATIC' ].has(names[n])) n--;
        if (n < 0 || names[n]!='T_DOC_COMMENT') return '';
        var comment = values[n];
        
        var m : TypedArray<String, String> = null;
		if (preg_match_ex("/@var\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)/", comment, m) > 0)
        {
            comment = preg_replace("/^\\s*[*]?\\s*@var\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*\n/m", "", comment);
            comment = preg_replace("/^\\s*[*]\\s*[\r\n]+/m", "", comment);
            values[n] = comment;
            return this.getHaxeType(m['type']);
        }
        
        return '';
    }
    
    private function processVar(names:Array<String>, values:Array<String>, i:Int) : Void
    {
        var prefix = values[i].startsWith("var ") ? "var " : "";
		values[i] = values[i].substr(prefix.length);
		
		if (values[i].startsWith('$')) values[i] = values[i].substr(1);
        
        var type = this.detectVarType(names, values, i);
        if (type!='')
        {
            values[i] = values[i] + " : " + type;
        }
		
        /*if (
            $i+3<count($names) && $names[$i+1]=='[' 
         && ($names[$i+2]=='T_CONSTANT_ENCAPSED_STRING' || $names[$i+2]=='T_ENCAPSED_AND_WHITESPACE')
        ) {
            $n = $this->getPairPos($names, $i+1);
            $values[$n] = ')';

            if (
                $this->isBeforeLexem($names, $n, '[', 1)
             || $this->isBeforeLexem($names, $n, ']', 1)
             || $this->isBeforeLexem($names, $n, ')', 1)
             || $this->isBeforeLexem($names, $n, '.', 1)
             || $this->isBeforeLexem($names, $n, '+', 1)
             || $this->isBeforeLexem($names, $n, '-', 1)
             || $this->isBeforeLexem($names, $n, '*', 1)
             || $this->isBeforeLexem($names, $n, '/', 1)
             || $this->isBeforeLexem($names, $n, ',', 1)
            ) {
                $values[$i+1] = '.get(';
            }
            else
            if ($this->isBeforeLexem($names, $n, '=', 1))
            {
                $values[$i+1] = '.set(';
            }
            else
            {
                $values[$i+1] = '.getset(';
            }
        }*/
		
        if (isset(this.varNamesMapping[values[i]]))
        {
            values[i] = this.varNamesMapping[values[i]];
        }
		
        if (i-1>=0 && names[i-1]=='T_ENCAPSED_AND_WHITESPACE')
        {
            values[i] = '" + ' + values[i];
        }
        else
        if (i-1>=0 && names[i-1]=='"')
        {
            values[i-1] = '';
        }
		
        if (i+1<names.length && names[i+1]=='T_ENCAPSED_AND_WHITESPACE')
        {
            values[i] = values[i] + ' + "';
        }
        else
        if (i+1<names.length && names[i+1]=='"')
        {
            values[i+1] = '';
        }
		
        if (i+1<names.length && names[i+1]=='T_VARIABLE')
        {
            values[i] += ' + ';
        }
		
        if (!this.isAfterLexem(names, i, ['T_FUNCTION'], 3)
         && this.isAfterLexem(names, i, [ 'T_PUBLIC','T_PRIVATE','T_STATIC' ], 3)
        ) {
            values[i] = 'var ' + values[i];
        }
		
		values[i] = prefix + values[i];
    }
    
    private function processFunctionCall(names:Array<String>, values:Array<String>, i:Int) : Int
    {
        if (isset(this.functionNameMapping[values[i]]))
        {
            var rval = this.functionNameMapping[values[i]];
            var newFuncName = Std.is(rval, Array) ? rval[0] : rval;
            values[i] = newFuncName;
            if (Std.is(rval, Array))
            {
				var rval : Array<Dynamic> = rval;
				
                if (i+1<names.length && names[i+1]=='(')
                {
                    var n = this.getPairPos(names, i+1);

                    var params = this.splitTokensByComma
					(
                        names.slice(i+2, n)
                      , values.slice(i+2, n)
                    );
					
                    var insertNames = [];
                    var insertValues = [];
					
                    var j = 0; while (j<rval.length)
                    {
                        var param : Dynamic = rval[j];
						
                        if (is_string(param))
                        {
                            insertNames.push('([{}])'.indexOf(param) >= 0 ? param : '_CORRECTED');
                            insertValues.push(param);
                        }
                        else
                        {
							if (isset(params[param]))
                            {
                                var killSkobki = 
                                        j>0 && rval[j-1]=='(' 
                                     && j+1<rval.length && rval[j+1]==')'
                                     && j+2<rval.length && rval[j+2]=='.'
                                     && this.isSolidExpression(params[param].names);
								
                                if (killSkobki)
                                {
                                    insertNames.pop();
                                    insertValues.pop();
                                }
								
                                insertNames = insertNames.concat(params[param].names);
                                insertValues = insertValues.concat(params[param].values);
								
                                if (killSkobki) j++;
                            }
                            else
                            {
                                if (j+2==rval.length && rval[j+1]==')' && j>1 && StringTools.rtrim(rval[j-1])==',')
                                {
                                    insertNames.pop();
                                    insertValues.pop();
                                }
                            }
                        }
						
						j++;
                    }
					
                    names .spliceEx(i, n-i+1, insertNames);
                    values.spliceEx(i, n-i+1, insertValues);
					
                    i--;
                }
            }
        }
		
		return i;
    }
}
