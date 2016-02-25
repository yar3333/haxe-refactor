<?php
require_once dirname(__FILE__) . "/php_to_haxe.config.php";

if (count($argv) < 3 || count($argv) > 4)
{
	echo "Usage: php_to_haxe code|extern <in_file.php> [<out_file.hx>]\n";
	die;
}
else
{
	$mode = $argv[1];
	$from = $argv[2];
	if (count($argv) >= 4)
	{
		$to = $argv[3];
	}
	else
	{
		list($dirname, $basename, $extension, $filename) = array_values(pathinfo($argv[2]));
		$to = ($dirname !== "" && $dirname !== "." ? "$dirname/" : "" ) . ucfirst($filename) . ".hx";
	}
	
	echo "$from => $to: ";
	try
	{
		$phpToHaxe = new PhpToHaxe($typeNamesMapping, $varNamesMapping, $functionNameMapping, $mode=="extern");
		if (!file_exists($from)) throw new Exception("Input file not exists.");
		$inp = file_get_contents($from);
		$out = $phpToHaxe->getHaxeCode($inp);
		file_put_contents($to, $out);
		echo "OK\n";
	}
	catch(Exception $e)
	{
		echo "FAIL\n";
		throw $e;
	}
} 

class PhpToHaxe
{
    private $typeNamesMapping;
    private $functionNameMapping;
    private $varNamesMapping;
    private $wantExtern;
    
    function __construct($typeNamesMapping, $varNamesMapping, $functionNameMapping, $wantExtern=false)
    {
        $this->typeNamesMapping = $typeNamesMapping;
        $this->varNamesMapping = $varNamesMapping;
        $this->functionNameMapping = $functionNameMapping;
        $this->wantExtern = $wantExtern;
    }

    function getHaxeCode($text)
    {
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);
        $text = preg_replace("/^(\\s*<[?]php)+/", "", $text);
        $tokens = token_get_all("<?php " . $text);

        $names = array();
        $values = array();
        foreach ($tokens as $token)
        {
            if (is_array($token))
            {
                $names[] = token_name($token[0]);
                $values[] = $token[1];
            }
            else
            {
                $names[] = $token;
                $values[] = $token;
            }
        }

        if (count($names)>0 && $names[0]=='T_OPEN_TAG')
        {
            array_shift($names);
            array_shift($values);
        }

        $this->changeProtectedToPrivate($names, $values);
        
        $r = $this->tokensToText($names, $values);
        if ($this->wantExtern) $r = preg_replace("/[\t ]*\n[\t ]*\n[\t ]*\n/", "\n\n", $r);
        return $r;
    }

    private function changeProtectedToPrivate(&$names, &$values)
    {
        for ($i=0; $i<count($names); $i++)
        {
            if ($names[$i]=='T_PROTECTED')
            {
                $names[$i] = 'T_PRIVATE';
                $values[$i] = 'protected';
            }
        }
    }
    
    private function isBeforeLexem($names, $n, $lexem, $dist)
    {
        for ($i=$n+1; $dist>0 && $i<count($names); $i++)
        {
            if ($names[$i]=='T_WHITESPACE' || $names[$i]=='T_COMMENT') continue;
            if (is_array($lexem))
            {
                if (in_array($names[$i], $lexem)) return true;
            }
            else
            {
                if ($names[$i]==$lexem) return true;
            }
            $dist--;
        }
        return false;
    }

    private function isAfterLexem($names, $n, $lexem, $dist)
    {
        for ($i=$n-1; $dist>0 && $i>=0; $i--)
        {
            if ($names[$i]=='T_WHITESPACE' || $names[$i]=='T_COMMENT') continue;
            if (is_array($lexem))
            {
                if (in_array($names[$i], $lexem)) return true;
            }
            else
            {
                if ($names[$i]==$lexem) return true;
            }
            $dist--;
        }
        return false;
    }

    private function getPairPos($names, $i)
    {
        $stack = array();
        for ($i=$i; $i<count($names); $i++)
        {
            if (in_array($names[$i], array('(','{','[')))
            {
                $stack[] = $names[$i];
            }
            else
            if (in_array($names[$i], array(')','}',']')))
            {
                $s = array_pop($stack);
                if (count($stack)==0) return $i;
            }            
        }
        die("Fatal error: pair not found.");
    }

    private function findLexemPosOnCurrentLevel($names, $i, $lexem)
    {
        $stack = array();
        for (; $i<count($names); $i++)
        {
            if ($names[$i]==$lexem && count($stack)==0)
            {
                return $i;
            }

            if (in_array($names[$i], array('(','{','['))) $stack[] = $names[$i];
            if (in_array($names[$i], array(')','}',']'))) array_pop($stack);
        }
        die("Fatal error: lexem '$lexem' not found from position $i.");
    }

    private function splitTokensByComma($names, $values)
    {
        $params = array();

        $param = array(
            'names' => array()
          , 'values' => array()
        );

        $stack = array();
        for ($i=0; $i<count($names); $i++)
        {
            if (in_array($names[$i], array('(','{','['))) $stack[] = $names[$i];
            if (in_array($names[$i], array(')','}',']'))) array_pop($stack);

            if ($names[$i]==',' && count($stack)==0)
            {
                $params[] = $param;
                $param = array(
                    'names' => array()
                  , 'values' => array()
                );
            }
            else
            {
                if (count($param['names']) > 0 || $names[$i]!='T_WHITESPACE')
                {
                    $param['names'][] = $names[$i];
                    $param['values'][] = $values[$i];
                }
            }
        }
        if (count($param['names']) > 0) $params[] = $param;

        return $params;
    }

    private function trimAndPad(&$names, &$values, $padLeft, $padRight)
    {
        while (count($names)>0 && $names[0]=='T_WHITESPACE')
        {
            array_shift($names);
            array_shift($values);
        }
        while (count($names)>0 && $names[count($names)-1]=='T_WHITESPACE')
        {
            array_pop($names);
            array_pop($values);
        }
        for ($i=0;$i<$padLeft;$i++)
        {
            array_unshift($names, 'T_WHITESPACE');
            array_unshift($values, ' ');
        }
        for ($i=0;$i<$padRight;$i++)
        {
            array_push($names, 'T_WHITESPACE');
            array_push($values, ' ');
        }
    }

    private function isSolidExpression($names)
    {
        $k = 0;
        foreach ($names as $name)
        {
            if ($name=='T_DOUBLE_COLON' || $name=='T_OBJECT_OPERATOR') $k-=2;
            if ($name!='T_WHITESPACE' && $name!='T_COMMENT')
            {
                $k++;
                if ($k>1) return false;
            }
        }
        return true;
    }

    private function tokensToText($names, $values)
    {
        $text = '';
        for ($i=0;$i<count($names);$i++)
        {
            //echo "{$names[$i]} " . str_replace("\n",' ',$values[$i]) . "\n";
            switch ($names[$i])
            {
                case '.':
                    $values[$i] = '+';
                    break;
                
                case 'T_CONCAT_EQUAL':
                    $values[$i] = '+=';
                    break;
                
                case 'T_CLASS':
                    if ($this->wantExtern)
                    {
                        $values[$i] = 'extern '.$values[$i];
                    }
                    break;

                case 'T_DOUBLE_COLON':
                    if ($i-1>=0 && $names[$i-1]=='T_STRING' && $values[$i-1]=='parent')
                    {
                        $values[$i-1] = 'super';
                    }
                    // no break!
                case 'T_OBJECT_OPERATOR':
                    if ($i-1>=0 && $names[$i-1]=='T_STRING' && $values[$i-1]=='self')
                    {
                        $names[$i-1] = 'T_COMMENT';
                        $values[$i-1] = '/*self.*/';
                        $names[$i] = 'T_WHITESPACE';
                        $values[$i] = '';
                    }
                    else
                    {
                        $values[$i] = '.';
                    }
                    break;

                case 'T_PRIVATE':
                    if ($this->wantExtern)
                    {
                        if ($this->isBeforeLexem($names, $i, 'T_VARIABLE', 2))
                        {
                            $beg = $i - 1;
                            while ($beg > 0 && in_array($names[$beg], array('T_STATIC', 'T_WHITESPACE', 'T_DOC_COMMENT'))) $beg--;
                            $beg++;
                            $end = $this->findLexemPosOnCurrentLevel($names, $i, ';');
                            array_splice($names,  $beg, $end - $beg + 1);
                            array_splice($values, $beg, $end - $beg + 1);
                            $i = $beg - 1;
                        }
                    }
                    break;
                case 'T_PUBLIC':
                    $values[$i] = 'public';
                    break;
                case 'T_STATIC':
                    $values[$i] = 'static';
                    break;
                case 'T_VARIABLE':
                    $this->processVar($names, $values, $i);
                    break;
                case 'T_STRING':
                    if ($this->isAfterLexem($names, $i, 'T_CONST', 1))
                    {
                        $type = $this->detectVarType($names, $values, $i);
                        if ($type!=='') $values[$i] = $values[$i] . " : $type";
                    }
                    else
                    {
                        $this->processFunctionCall($names, $values, $i);
                    }
                    break;
                case 'T_ARRAY':
                    if ($i+1<count($names) && $names[$i+1]=='(')
                    {
                        $values[$i] = '';
                        $names[$i+1] = '[';
                        $values[$i+1] = '[ ';
                        $n = $this->getPairPos($names, $i+1);
                        $names[$n] = ']';
                        $values[$n] = ' ]';

                        if ($n-$i==2)
                        {
                            $values[$i+1] = '[';
                            $values[$n] = ']';
                        }
                    }
                    break;
                case 'T_FUNCTION':
                    $this->processFunction($names, $values, $i);
                    break;
                case 'T_CONST':
                    $values[$i] = 'static inline public var';
                    break;
                case 'T_INCLUDE_ONCE':
                case 'T_REQUIRE_ONCE':
                    $values[$i] = 'import'; $i++;
                    while ($names[$i]=='T_WHITESPACE') $i++;
                    $values[$i] = "/*" . $values[$i]; $i++;
                    while ($names[$i]!=';') $i++;
                    $values[$i] = "*/;";
                    break;
                case 'T_FOREACH':
                    $values[$i] = 'for';

                    $parBegin = $this->findLexemPosOnCurrentLevel($names, $i+1, '(');

                    $asPos = $this->findLexemPosOnCurrentLevel($names, $parBegin+1, 'T_AS');
                    $names[$asPos] = '__PROCESSED';
                    $values[$asPos] = 'in';

                    $namesList  = array_splice($names,  $parBegin+1, $asPos - $parBegin-1);
                    $valuesList = array_splice($values, $parBegin+1, $asPos - $parBegin-1);
                    $this->trimAndPad($namesList, $valuesList, 1, 0);

                    $parEnd = $this->getPairPos($names, $parBegin);

                    $namesVar =  array_splice($names,  $parBegin+2, $parEnd - $parBegin-2, $namesList);
                    $valuesVar = array_splice($values, $parBegin+2, $parEnd - $parBegin-2, $valuesList);
                    $this->trimAndPad($namesVar, $valuesVar, 0, 1);

                    array_splice($names,  $parBegin+1, 0, $namesVar);
                    array_splice($values, $parBegin+1, 0, $valuesVar);
            }
        }

        return implode($values);
    }
    
    private function processFunction(&$names, &$values, &$i)
    {
        $phpEmptyArray = 'untyped __php__("array()")';
        
        if ($this->wantExtern)
        {
            if ($this->isAfterLexem($names, $i, 'T_PRIVATE', 2))
            {
                $begFunc = $i-1;
                while ($begFunc>0 && in_array($names[$begFunc], array('T_DOC_COMMENT', 'T_PRIVATE', 'T_STATIC', 'T_WHITESPACE'))) $begFunc--;
                $begFunc++;
                
                $endFunc = $i+1;
                while (!in_array($names[$endFunc], array(';', '{'))) $endFunc++;
                
                if ($names[$endFunc]=='{')
                {
                    $endFunc = $this->getPairPos($names, $endFunc);
                    array_splice($names, $begFunc, $endFunc - $begFunc + 1);
                    array_splice($values, $begFunc, $endFunc - $begFunc + 1);
                    $i = $begFunc;
                    return;
                }
            }
        }
        
        if (!$this->isAfterLexem($names, $i, array('T_PUBLIC', 'T_PRIVATE'), 2))
        {
            $values[$i] = 'public ' . $values[$i];
        }
        
        $commentIndex = $i - 1;
        while ($commentIndex > 0 && in_array($names[$commentIndex], array('T_WHITESPACE', 'T_PUBLIC', 'T_PRIVATE', 'T_STATIC'))) $commentIndex--; 
        
        $commentVarTypes = array();
        $returnType = 'void';
        if ($commentIndex>=0 && $names[$commentIndex]=='T_DOC_COMMENT' )
        {
            $commentVarTypes = $this->getVarTypesByDocComment($values[$commentIndex]);
            $returnType = $this->getReturnTypesByDocComment($values[$commentIndex]);
            $this->processDocComment($names, $values, $commentIndex);
        }
        
        $n = $i + 1;
        while ($names[$n]=='T_WHITESPACE') $n++;
        if ($names[$n]!='T_STRING') return;
        $methodName = $values[$n];
        
        $n++;
        while ($names[$n]=='T_WHITESPACE') $n++;
        if ($names[$n]!='(') return;
        $begParamsIndex = $n;
        $endParamsIndex = $this->getPairPos($names, $n);
        
        $params = $this->splitTokensByComma(
             array_slice($names,  $begParamsIndex + 1, $endParamsIndex - $begParamsIndex - 1)
            ,array_slice($values, $begParamsIndex + 1, $endParamsIndex - $begParamsIndex - 1)
        );
        
        $resParamsStr = array();
        foreach ($params as $param)
        {
            $paramNames = $param['names'];
            $paramValues = $param['values'];
            $this->trimAndPad($paramNames, $paramValues, 0, 0);
            
            $type = '';
            $name = '';
            $defVal = '';
            
            if (count($paramNames) > 1 && $paramNames[0] == 'T_STRING')
            {
                $type = $paramValues[0];
                array_shift($paramNames); array_shift($paramValues);
                $this->trimAndPad($paramNames, $paramValues, 0, 0);
            }
            
            if (count($paramNames) > 0 && $paramNames[0] == 'T_VARIABLE')
            {
                $name = substr($paramValues[0], 1);
                array_shift($paramNames); array_shift($paramValues);
                $this->trimAndPad($paramNames, $paramValues, 0, 0);
            }
            
            if (count($paramNames) > 0 && $paramNames[0] == '=')
            {
                array_shift($paramNames); array_shift($paramValues);
                $this->trimAndPad($paramNames, $paramValues, 0, 0);
                if (count($paramNames) > 0)
                {
                    $defVal = $paramValues[0];
                    array_shift($paramNames); array_shift($paramValues);
                    $this->trimAndPad($paramNames, $paramValues, 0, 0);
                    if (count($paramNames)>=2 && $paramNames[0]=='(' && $paramNames[1]==')')
                    {
                        $defVal .= "()";
                        array_shift($paramNames); array_shift($paramValues);
                        array_shift($paramNames); array_shift($paramValues);
                        
                    }
                    else if (count($paramNames)>=3 && $paramNames[0]=='(' && $paramNames[1]=='T_WHITESPACE' && $paramNames[2]==')')
                    {
                        $defVal .= "()";
                        array_shift($paramNames); array_shift($paramValues);
                        array_shift($paramNames); array_shift($paramValues);
                        array_shift($paramNames); array_shift($paramValues);
                    }
                        
                        
                    if ($defVal=='array()') $defVal = $phpEmptyArray;
                }
                
            }
            
            if ($type == '' && isset($commentVarTypes[$name]))
            {
                $type = $commentVarTypes[$name];
            }
            
            if ($type=='')
            {
                if ($defVal=='true' || $defVal=='false') $type = 'Bool';
                else
                if ($defVal==$phpEmptyArray) $type = 'NativeArray';
            }
            
            $resParamsStr[] = $name . ($type!='' ? ':'.$this->getHaxeType($type):'') . ($defVal!=='' ? '='.$defVal : '');
        }
        
        array_splice($names,  $begParamsIndex+1, $endParamsIndex-$begParamsIndex-1, array('T_COMMENT'));
        array_splice($values, $begParamsIndex+1, $endParamsIndex-$begParamsIndex-1, array(implode(', ', $resParamsStr)));
        
        $i = $begParamsIndex + 2;
        
        if ($returnType!='')
        {
            array_splice($names,  $begParamsIndex+3, 0, array('T_COMMENT'));
            array_splice($values, $begParamsIndex+3, 0, array(" : " . $this->getHaxeType($returnType)));
            $i++;
        }
        
        if ($this->wantExtern)
        {
            $funcBeg = $i + 1;
            while ($names[$funcBeg]=='T_WHITESPACE') $funcBeg++;
            if ($values[$funcBeg] == '{')
            {
                $funcEnd = $this->getPairPos($names, $funcBeg);
                array_splice($names,  $i+1, $funcEnd-$i, array(';'));
                array_splice($values, $i+1, $funcEnd-$i, array(';'));
                $i = $funcBeg;
            }
            
        }
    }
    
    private function getVarTypesByDocComment($comment)
    {
        $r = array();
        
        if (preg_match_all("/@param\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)\\s+[\$]?(?<name>[_a-zA-Z][_a-zA-Z0-9]*)/", $comment, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as $m)
            {
                $r[$m['name']] = $m['type'];
            }
        }
        
        return $r;
    }
    
    private function getReturnTypesByDocComment($comment)
    {
        if (preg_match("/@return\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)/", $comment, $m))
        {
            return $m['type'];
        }
        return '';
    }
    
    private function getHaxeType($phpType)
    {
        if (isset($this->typeNamesMapping[$phpType]))
        {
            return $this->typeNamesMapping[$phpType];
        }
        return $phpType;
    }
    
    private function processDocComment(&$names, &$values, &$i)
    {
        $comment = $values[$i];
        
        $comment = preg_replace("/(@param\\s+)[_a-zA-Z][_a-zA-Z0-9]*\\s+[\$]([_a-zA-Z0-9]*)/", "\\1\\2", $comment);
        $comment = preg_replace("/^\\s*[*]\\s*@param\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*[\r\n]+/m", "", $comment);
        $comment = preg_replace("/^\\s*[*]\\s*@return\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*[\r\n]+/m", "", $comment);
        $comment = preg_replace("/^\\s*[*]\\s*[\r\n]+/m", "", $comment);
        
        $values[$i] = $comment;
    }

    private function detectVarType(&$names, &$values, $i)
    {
        $n = $i - 1;
        while ($n > 0 && in_array($names[$n], array('T_WHITESPACE','T_PUBLIC','T_PRIVATE','T_CONST','T_STATIC'))) $n--;
        if ($n < 0 || $names[$n]!='T_DOC_COMMENT') return '';
        $comment = $values[$n];
        
        if (preg_match("/@var\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)/", $comment, $m))
        {
            $comment = preg_replace("/^\\s*[*]?\\s*@var\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*\n/m", "", $comment);
            $comment = preg_replace("/^\\s*[*]\\s*[\r\n]+/m", "", $comment);
            $values[$n] = $comment;
            return $this->getHaxeType($m['type']);
        }
        
        return '';
    }
    
    private function processVar(&$names, &$values, &$i)
    {
        if (substr($values[$i],0,1)=='$') $values[$i] = substr($values[$i],1);
        
        $type = $this->detectVarType($names, $values, $i);
        if ($type!=='')
        {
            $values[$i] = $values[$i] . " : $type";
        }

        if (
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
        }

        if (isset($this->varNamesMapping[$values[$i]]))
        {
            $values[$i] = $this->varNamesMapping[$values[$i]];
        }

        if ($i-1>=0 && $names[$i-1]=='T_ENCAPSED_AND_WHITESPACE')
        {
            $values[$i] = '" + ' . $values[$i];
        }
        else
        if ($i-1>=0 && $names[$i-1]=='"')
        {
            $values[$i-1] = '';
        }

        if ($i+1<count($names) && $names[$i+1]=='T_ENCAPSED_AND_WHITESPACE')
        {
            $values[$i] = $values[$i] . ' + "';
        }
        else
        if ($i+1<count($names) && $names[$i+1]=='"')
        {
            $values[$i+1] = '';
        }

        if ($i+1<count($names) && $names[$i+1]=='T_VARIABLE')
        {
            $values[$i] .= ' + ';
        }

        if (!$this->isAfterLexem($names, $i, 'T_FUNCTION', 3)
         && $this->isAfterLexem($names, $i, array('T_PUBLIC','T_PRIVATE','T_STATIC'), 3)
        ) {
            $values[$i] = 'var ' . $values[$i];
        }
    }
    
    private function processFunctionCall(&$names, &$values, &$i)
    {
        if (isset($this->functionNameMapping[$values[$i]]))
        {
            $rval = $this->functionNameMapping[$values[$i]];
            $newFuncName = is_string($rval) ? $rval : $rval[0];
            $values[$i] = $newFuncName;
            if (is_array($rval))
            {
                if ($i+1<count($names) && $names[$i+1]=='(')
                {
                    $n = $this->getPairPos($names, $i+1);

                    $params = $this->splitTokensByComma(
                        array_slice($names,  $i+2, $n-$i-2)
                      , array_slice($values, $i+2, $n-$i-2)
                    );

                    $insertNames = array();
                    $insertValues = array();

                    for ($j=0; $j<count($rval); $j++)
                    {
                        $param = $rval[$j];

                        if (is_string($param))
                        {
                            $insertNames[] = strpos('([{}])', $param)!==false ? $param : '_CORRECTED';
                            $insertValues[] = $param;
                        }
                        else
                        {
                            if (isset($params[$param]))
                            {
                                $killSkobki = 
                                        $j>0 && $rval[$j-1]=='(' 
                                     && $j+1<count($rval) && $rval[$j+1]==')'
                                     && $j+2<count($rval) && $rval[$j+2]=='.'
                                     && $this->isSolidExpression($params[$param]['names']);

                                if ($killSkobki)
                                {
                                    array_pop($insertNames);
                                    array_pop($insertValues);
                                }

                                $insertNames = array_merge($insertNames, $params[$param]['names']);
                                $insertValues = array_merge($insertValues, $params[$param]['values']);

                                if ($killSkobki) $j++;
                            }
                            else
                            {
                                if ($j+2==count($rval) && $rval[$j+1]==')' && $j>1 && rtrim($rval[$j-1])==',')
                                {
                                    array_pop($insertNames);
                                    array_pop($insertValues);
                                }
                            }
                        }
                    }

                    array_splice($names, $i, $n-$i+1, $insertNames);
                    array_splice($values, $i, $n-$i+1, $insertValues);

                    $i--;
                }
            }
        }
    }
}
