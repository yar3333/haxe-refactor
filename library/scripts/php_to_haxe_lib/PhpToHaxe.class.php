<?php

class PhpToHaxe {
	public function __construct($typeNamesMapping, $varNamesMapping, $functionNameMapping, $magickFunctionNameMapping, $reservedWords, $wantExtern = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("PhpToHaxe::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($wantExtern === null) {
			$wantExtern = false;
		}
		$this->typeNamesMapping = $typeNamesMapping;
		$this->varNamesMapping = $varNamesMapping;
		$this->functionNameMapping = $functionNameMapping;
		$this->magickFunctionNameMapping = $magickFunctionNameMapping;
		$this->wantExtern = $wantExtern;
		$this->reservedWords = $reservedWords;
		$GLOBALS['%s']->pop();
	}}
	public $typeNamesMapping;
	public $functionNameMapping;
	public $varNamesMapping;
	public $wantExtern;
	public $reservedWords;
	public $magickFunctionNameMapping;
	public function getHaxeCode($text) {
		$GLOBALS['%s']->push("PhpToHaxe::getHaxeCode");
		$__hx__spos = $GLOBALS['%s']->length;
		$text = str_replace("\x0D\x0A", "\x0A", $text);
		$text = str_replace("\x0D", "\x0A", $text);
		$text = _hx_deref(new EReg("^(\\s*<[?]php)+", ""))->replace($text, "");
		$tokens = token_get_all("<?php " . _hx_string_or_null($text));
		$names = new _hx_array(array());
		$values = new _hx_array(array());
		{
			$_g = 0;
			while($_g < count($tokens)) {
				$token = $tokens[$_g];
				++$_g;
				if(is_array($token)) {
					$names->push(PhpToHaxe_0($this, $_g, $names, $text, $token, $tokens, $values));
					$values->push($token[1]);
				} else {
					$names->push($token);
					$values->push($token);
				}
				unset($token);
			}
		}
		if($names->length > 0 && $names[0] === "T_OPEN_TAG") {
			$names->shift();
			$values->shift();
		}
		$this->changeProtectedToPrivate($names, $values);
		$this->changeStdValuesToLowerCase($names, $values);
		$this->changeOctalNumberToHex($names, $values);
		$this->changeReservedWords($names, $values);
		$this->changeIsIdenticalToIsEqual($names, $values);
		$r = $this->tokensToText($names, $values);
		if($this->wantExtern) {
			$r = _hx_deref(new EReg("[\x09 ]*\x0A[\x09 ]*\x0A[\x09 ]*\x0A", "g"))->replace($r, "\x0A\x0A");
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	public function changeProtectedToPrivate($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::changeProtectedToPrivate");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g1 = 0;
		$_g = $names->length;
		while($_g1 < $_g) {
			$i = $_g1++;
			if($names[$i] === "T_PROTECTED") {
				$names[$i] = "T_PRIVATE";
				$values[$i] = "private";
			}
			unset($i);
		}
		$GLOBALS['%s']->pop();
	}
	public function changeStdValuesToLowerCase($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::changeStdValuesToLowerCase");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g1 = 0;
		$_g = $names->length;
		while($_g1 < $_g) {
			$i = $_g1++;
			if($names[$i] === "T_STRING") {
				$lc = strtolower($values[$i]);
				switch($lc) {
				case "true":{}break;
				case "false":{}break;
				case "null":{
					$values[$i] = $lc;
				}break;
				}
				unset($lc);
			}
			unset($i);
		}
		$GLOBALS['%s']->pop();
	}
	public function changeOctalNumberToHex($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::changeOctalNumberToHex");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g1 = 0;
		$_g = $names->length;
		while($_g1 < $_g) {
			$i = $_g1++;
			if($names[$i] === "T_LNUMBER") {
				$s = $values[$i];
				if(_hx_char_at($s, 0) === "0" && strlen($s) > 1) {
					$values[$i] = "/*" . _hx_string_or_null($s) . "*/0x" . _hx_string_or_null(base_convert($s, 8, 16));
				}
				unset($s);
			}
			unset($i);
		}
		$GLOBALS['%s']->pop();
	}
	public function changeReservedWords($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::changeReservedWords");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g1 = 0;
		$_g = $names->length;
		while($_g1 < $_g) {
			$i = $_g1++;
			if($names[$i] === "T_VARIABLE") {
				$s = $values[$i];
				if($this->reservedWords->indexOf($s, null) >= 0) {
					$values[$i] = _hx_string_or_null($s) . "_";
				}
				unset($s);
			}
			unset($i);
		}
		$GLOBALS['%s']->pop();
	}
	public function changeIsIdenticalToIsEqual($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::changeIsIdenticalToIsEqual");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g1 = 0;
		$_g = $names->length;
		while($_g1 < $_g) {
			$i = $_g1++;
			if($names[$i] === "T_IS_IDENTICAL") {
				$values[$i] = "==";
			}
			unset($i);
		}
		$GLOBALS['%s']->pop();
	}
	public function isBeforeLexem($names, $n, $lexems, $dist) {
		$GLOBALS['%s']->push("PhpToHaxe::isBeforeLexem");
		$__hx__spos = $GLOBALS['%s']->length;
		$i = $n + 1;
		while($dist > 0 && $i < $names->length) {
			if($names[$i] === "T_WHITESPACE" || $names[$i] === "T_COMMENT") {
				$i++;
				continue;
			}
			if(Lambda::has($lexems, $names[$i])) {
				$GLOBALS['%s']->pop();
				return true;
			}
			$dist--;
			$i++;
		}
		{
			$GLOBALS['%s']->pop();
			return false;
		}
		$GLOBALS['%s']->pop();
	}
	public function isAfterLexem($names, $n, $lexems, $dist) {
		$GLOBALS['%s']->push("PhpToHaxe::isAfterLexem");
		$__hx__spos = $GLOBALS['%s']->length;
		$i = $n - 1;
		while($dist > 0 && $i >= 0) {
			if($names[$i] === "T_WHITESPACE" || $names[$i] === "T_COMMENT") {
				$i--;
				continue;
			}
			if(Lambda::has($lexems, $names[$i])) {
				$GLOBALS['%s']->pop();
				return true;
			}
			$dist--;
			$i--;
		}
		{
			$GLOBALS['%s']->pop();
			return false;
		}
		$GLOBALS['%s']->pop();
	}
	public function getPairPos($names, $i) {
		$GLOBALS['%s']->push("PhpToHaxe::getPairPos");
		$__hx__spos = $GLOBALS['%s']->length;
		$stack = (new _hx_array(array()));
		while($i < $names->length) {
			if(Lambda::has((new _hx_array(array("(", "{", "["))), $names[$i])) {
				$stack->push($names[$i]);
			} else {
				if(Lambda::has((new _hx_array(array(")", "}", "]"))), $names[$i])) {
					$stack->pop();
					if($stack->length === 0) {
						$GLOBALS['%s']->pop();
						return $i;
					}
				}
			}
			$i++;
		}
		throw new HException("Fatal error: pair not found.");
		$GLOBALS['%s']->pop();
	}
	public function findLexemPosOnCurrentLevel($names, $i, $lexem) {
		$GLOBALS['%s']->push("PhpToHaxe::findLexemPosOnCurrentLevel");
		$__hx__spos = $GLOBALS['%s']->length;
		$stack = (new _hx_array(array()));
		while($i < $names->length) {
			if($names[$i] === $lexem && $stack->length === 0) {
				$GLOBALS['%s']->pop();
				return $i;
			}
			if(Lambda::has((new _hx_array(array("(", "{", "["))), $names[$i])) {
				$stack->push($names[$i]);
			}
			if(Lambda::has((new _hx_array(array(")", "}", "]"))), $names[$i])) {
				$stack->pop();
			}
			$i++;
		}
		throw new HException("Fatal error: lexem '" . _hx_string_or_null($lexem) . "' not found from position " . _hx_string_rec($i, "") . ".");
		$GLOBALS['%s']->pop();
	}
	public function splitTokensByComma($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::splitTokensByComma");
		$__hx__spos = $GLOBALS['%s']->length;
		$params = (new _hx_array(array()));
		$param = _hx_anonymous(array("names" => (new _hx_array(array())), "values" => (new _hx_array(array()))));
		$stack = (new _hx_array(array()));
		{
			$_g1 = 0;
			$_g = $names->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if(Lambda::has((new _hx_array(array("(", "{", "["))), $names[$i])) {
					$stack->push($names[$i]);
				}
				if(Lambda::has((new _hx_array(array(")", "}", "]"))), $names[$i])) {
					$stack->pop();
				}
				if($names[$i] === "," && $stack->length === 0) {
					$params->push($param);
					$param = _hx_anonymous(array("names" => (new _hx_array(array())), "values" => (new _hx_array(array()))));
				} else {
					if($param->names->length > 0 || $names[$i] !== "T_WHITESPACE") {
						$param->names->push($names[$i]);
						$param->values->push($values[$i]);
					}
				}
				unset($i);
			}
		}
		if($param->names->length > 0) {
			$params->push($param);
		}
		{
			$GLOBALS['%s']->pop();
			return $params;
		}
		$GLOBALS['%s']->pop();
	}
	public function trimAndPad($names, $values, $padLeft, $padRight) {
		$GLOBALS['%s']->push("PhpToHaxe::trimAndPad");
		$__hx__spos = $GLOBALS['%s']->length;
		while($names->length > 0 && $names[0] === "T_WHITESPACE") {
			$names->shift();
			$values->shift();
		}
		while($names->length > 0 && $names[$names->length - 1] === "T_WHITESPACE") {
			$names->pop();
			$values->pop();
		}
		{
			$_g = 0;
			while($_g < $padLeft) {
				$i = $_g++;
				$names->unshift("T_WHITESPACE");
				$values->unshift(" ");
				unset($i);
			}
		}
		{
			$_g1 = 0;
			while($_g1 < $padRight) {
				$i1 = $_g1++;
				$names->push("T_WHITESPACE");
				$values->push(" ");
				unset($i1);
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function isSolidExpression($names) {
		$GLOBALS['%s']->push("PhpToHaxe::isSolidExpression");
		$__hx__spos = $GLOBALS['%s']->length;
		$k = 0;
		{
			$_g = 0;
			while($_g < $names->length) {
				$name = $names[$_g];
				++$_g;
				if($name === "T_DOUBLE_COLON" || $name === "T_OBJECT_OPERATOR") {
					$k -= 2;
				}
				if($name !== "T_WHITESPACE" && $name !== "T_COMMENT") {
					$k++;
					if($k > 1) {
						$GLOBALS['%s']->pop();
						return false;
					}
				}
				unset($name);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function tokensToText($names, $values) {
		$GLOBALS['%s']->push("PhpToHaxe::tokensToText");
		$__hx__spos = $GLOBALS['%s']->length;
		$text = "";
		$i = 0;
		while($i < $names->length) {
			{
				$_g = $names[$i];
				switch($_g) {
				case ".":{
					$values[$i] = "+";
				}break;
				case "T_CONCAT_EQUAL":{
					$values[$i] = "+=";
				}break;
				case "T_CLASS":{
					if($this->wantExtern) {
						$values[$i] = "extern " . _hx_string_or_null($values[$i]);
					}
				}break;
				case "T_DOUBLE_COLON":{
					if($i - 1 >= 0 && $names[$i - 1] === "T_STRING" && $values[$i - 1] === "parent") {
						$values[$i - 1] = "super";
					}
					if($i - 1 >= 0 && $names[$i - 1] === "T_STRING" && $values[$i - 1] === "self") {
						$names[$i - 1] = "T_COMMENT";
						$values[$i - 1] = "/*self.*/";
						$names[$i] = "T_WHITESPACE";
						$values[$i] = "";
					} else {
						$values[$i] = ".";
					}
				}break;
				case "T_OBJECT_OPERATOR":{
					if($i - 1 >= 0 && $names[$i - 1] === "T_STRING" && $values[$i - 1] === "self") {
						$names[$i - 1] = "T_COMMENT";
						$values[$i - 1] = "/*self.*/";
						$names[$i] = "T_WHITESPACE";
						$values[$i] = "";
					} else {
						$values[$i] = ".";
					}
				}break;
				case "T_PRIVATE":{
					if($this->wantExtern) {
						if($this->isBeforeLexem($names, $i, (new _hx_array(array("T_VARIABLE"))), 2)) {
							$beg = $i - 1;
							while($beg > 0 && Lambda::has((new _hx_array(array("T_STATIC", "T_WHITESPACE", "T_DOC_COMMENT"))), $names[$beg])) {
								$beg--;
							}
							$beg++;
							$end = $this->findLexemPosOnCurrentLevel($names, $i, ";");
							$names->splice($beg, $end - $beg + 1);
							$values->splice($beg, $end - $beg + 1);
							$i = $beg - 1;
						}
					}
				}break;
				case "T_PUBLIC":{
					$values[$i] = "public";
				}break;
				case "T_STATIC":{
					$values[$i] = "static";
				}break;
				case "T_VARIABLE":{
					$this->processVar($names, $values, $i);
				}break;
				case "T_STRING":{
					if($this->isAfterLexem($names, $i, (new _hx_array(array("T_CONST"))), 1)) {
						$type = $this->detectVarType($names, $values, $i);
						if($type !== "") {
							$values[$i] = _hx_string_or_null($values[$i]) . " : " . _hx_string_or_null($type);
						}
					} else {
						$i = $this->processFunctionCall($names, $values, $i);
					}
				}break;
				case "T_ARRAY":{
					if($i + 1 < $names->length && $names[$i + 1] === "(") {
						$values[$i] = "";
						$names[$i + 1] = "[";
						$values[$i + 1] = "[ ";
						$n = $this->getPairPos($names, $i + 1);
						$names[$n] = "]";
						$values[$n] = " ]";
						if($n - $i === 2) {
							$values[$i + 1] = "[";
							$values[$n] = "]";
						}
					}
				}break;
				case "T_FUNCTION":{
					$i = $this->processFunction($names, $values, $i);
				}break;
				case "T_CONST":{
					$values[$i] = "public static inline var";
				}break;
				case "T_INCLUDE_ONCE":case "T_REQUIRE_ONCE":{
					$values[$i] = "import";
					$i++;
					while($names[$i] === "T_WHITESPACE") {
						$i++;
					}
					$values[$i] = "/*" . _hx_string_or_null($values[$i]);
					$i++;
					while($names[$i] !== ";") {
						$i++;
					}
					$values[$i] = "*/;";
				}break;
				case "T_FOREACH":{
					$values[$i] = "for";
					$parBegin = $this->findLexemPosOnCurrentLevel($names, $i + 1, "(");
					$asPos = $this->findLexemPosOnCurrentLevel($names, $parBegin + 1, "T_AS");
					$names[$asPos] = "__PROCESSED";
					$values[$asPos] = "in";
					$namesList = $names->splice($parBegin + 1, $asPos - $parBegin - 1);
					$valuesList = $values->splice($parBegin + 1, $asPos - $parBegin - 1);
					$this->trimAndPad($namesList, $valuesList, 1, 0);
					$parEnd = $this->getPairPos($names, $parBegin);
					$namesVar = stdlib_LambdaArray::spliceEx($names, $parBegin + 2, $parEnd - $parBegin - 2, $namesList);
					$valuesVar = stdlib_LambdaArray::spliceEx($values, $parBegin + 2, $parEnd - $parBegin - 2, $valuesList);
					$this->trimAndPad($namesVar, $valuesVar, 0, 1);
					stdlib_LambdaArray::spliceEx($names, $parBegin + 1, 0, $namesVar);
					stdlib_LambdaArray::spliceEx($values, $parBegin + 1, 0, $valuesVar);
				}break;
				}
				unset($_g);
			}
			$i++;
		}
		{
			$tmp = $values->join("");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function processFunction($names, $values, $i) {
		$GLOBALS['%s']->push("PhpToHaxe::processFunction");
		$__hx__spos = $GLOBALS['%s']->length;
		$phpEmptyArray = "untyped __php__(\"array()\")";
		if($this->wantExtern) {
			if($this->isAfterLexem($names, $i, (new _hx_array(array("T_PRIVATE"))), 2)) {
				$begFunc = $i - 1;
				while($begFunc > 0 && Lambda::has((new _hx_array(array("T_DOC_COMMENT", "T_PRIVATE", "T_STATIC", "T_WHITESPACE"))), $names[$begFunc])) {
					$begFunc--;
				}
				$begFunc++;
				$endFunc = $i + 1;
				while(!Lambda::has((new _hx_array(array(";", "{"))), $names[$endFunc])) {
					$endFunc++;
				}
				if($names[$endFunc] === "{") {
					$endFunc = $this->getPairPos($names, $endFunc);
					$names->splice($begFunc, $endFunc - $begFunc + 1);
					$values->splice($begFunc, $endFunc - $begFunc + 1);
					$i = $begFunc;
					{
						$GLOBALS['%s']->pop();
						return $i;
					}
				}
			}
		}
		if(!$this->isAfterLexem($names, $i, (new _hx_array(array("T_PUBLIC", "T_PRIVATE"))), 2)) {
			$values[$i] = "public " . _hx_string_or_null($values[$i]);
		}
		$commentIndex = $i - 1;
		while($commentIndex > 0 && Lambda::has((new _hx_array(array("T_WHITESPACE", "T_PUBLIC", "T_PRIVATE", "T_STATIC"))), $names[$commentIndex])) {
			$commentIndex--;
		}
		$commentVarTypes = null;
		{
			$this1 = null;
			$this1 = array();
			$commentVarTypes = $this1;
		}
		$returnType = "void";
		if($commentIndex >= 0 && $names[$commentIndex] === "T_DOC_COMMENT") {
			$commentVarTypes = $this->getVarTypesByDocComment($values[$commentIndex]);
			$returnType = $this->getReturnTypesByDocComment($values[$commentIndex]);
			$this->processDocComment($names, $values, $commentIndex);
		}
		$n = $i + 1;
		while($names[$n] === "T_WHITESPACE") {
			$n++;
		}
		if($names[$n] !== "T_STRING") {
			$GLOBALS['%s']->pop();
			return $i;
		}
		if($this->magickFunctionNameMapping->exists($values[$n])) {
			$values[$n] = $this->magickFunctionNameMapping->get($values[$n]);
		}
		$methodName = $values[$n];
		$n++;
		while($names[$n] === "T_WHITESPACE") {
			$n++;
		}
		if($names[$n] !== "(") {
			$GLOBALS['%s']->pop();
			return $i;
		}
		$begParamsIndex = $n;
		$endParamsIndex = $this->getPairPos($names, $n);
		$params = $this->splitTokensByComma($names->slice($begParamsIndex + 1, $endParamsIndex), $values->slice($begParamsIndex + 1, $endParamsIndex));
		$resParamsStr = (new _hx_array(array()));
		$vars = (new _hx_array(array()));
		{
			$_g = 0;
			while($_g < $params->length) {
				$param = $params[$_g];
				++$_g;
				$paramNames = $param->names;
				$paramValues = $param->values;
				$this->trimAndPad($paramNames, $paramValues, 0, 0);
				$type = "";
				$name = "";
				$defVal = "";
				if($paramNames->length > 1 && ($paramNames[0] === "T_STRING" || $paramNames[0] === "T_ARRAY")) {
					$type = $paramValues[0];
					$paramNames->shift();
					$paramValues->shift();
					$this->trimAndPad($paramNames, $paramValues, 0, 0);
				}
				if($paramNames->length > 0 && $paramNames[0] === "T_VARIABLE") {
					$name = _hx_substr($paramValues[0], 1, null);
					$paramNames->shift();
					$paramValues->shift();
					$this->trimAndPad($paramNames, $paramValues, 0, 0);
				}
				if($paramNames->length > 0 && $paramNames[0] === "=") {
					$paramNames->shift();
					$paramValues->shift();
					$this->trimAndPad($paramNames, $paramValues, 0, 0);
					if($paramNames->length > 0) {
						$defVal = $paramValues[0];
						$paramNames->shift();
						$paramValues->shift();
						$this->trimAndPad($paramNames, $paramValues, 0, 0);
						if($paramNames->length >= 2 && $paramNames[0] === "(" && $paramNames[1] === ")") {
							$defVal .= "()";
							$paramNames->shift();
							$paramValues->shift();
							$paramNames->shift();
							$paramValues->shift();
						} else {
							if($paramNames->length >= 3 && $paramNames[0] === "(" && $paramNames[1] === "T_WHITESPACE" && $paramNames[2] === ")") {
								$defVal .= "()";
								$paramNames->shift();
								$paramValues->shift();
								$paramNames->shift();
								$paramValues->shift();
								$paramNames->shift();
								$paramValues->shift();
							}
						}
						if($defVal === "array()") {
							$defVal = $phpEmptyArray;
						}
					}
				}
				if($type === "" && isset($commentVarTypes[$name])) {
					$type = $commentVarTypes[$name];
				}
				if($type === "") {
					if($defVal === "true" || $defVal === "false") {
						$type = "Bool";
					} else {
						if($defVal === $phpEmptyArray) {
							$type = "NativeArray";
						}
					}
				}
				$resParamsStr->push(_hx_string_or_null($name) . _hx_string_or_null((PhpToHaxe_1($this, $_g, $begParamsIndex, $commentIndex, $commentVarTypes, $defVal, $endParamsIndex, $i, $methodName, $n, $name, $names, $param, $paramNames, $paramValues, $params, $phpEmptyArray, $resParamsStr, $returnType, $type, $values, $vars))) . _hx_string_or_null((PhpToHaxe_2($this, $_g, $begParamsIndex, $commentIndex, $commentVarTypes, $defVal, $endParamsIndex, $i, $methodName, $n, $name, $names, $param, $paramNames, $paramValues, $params, $phpEmptyArray, $resParamsStr, $returnType, $type, $values, $vars))));
				$vars->push($name);
				unset($type,$paramValues,$paramNames,$param,$name,$defVal);
			}
		}
		stdlib_LambdaArray::spliceEx($names, $begParamsIndex + 1, $endParamsIndex - $begParamsIndex - 1, (new _hx_array(array("T_COMMENT"))));
		stdlib_LambdaArray::spliceEx($values, $begParamsIndex + 1, $endParamsIndex - $begParamsIndex - 1, (new _hx_array(array($resParamsStr->join(", ")))));
		$i = $begParamsIndex + 2;
		if($returnType !== "") {
			stdlib_LambdaArray::spliceEx($names, $begParamsIndex + 3, 0, (new _hx_array(array("T_COMMENT"))));
			stdlib_LambdaArray::spliceEx($values, $begParamsIndex + 3, 0, (new _hx_array(array(" : " . _hx_string_or_null($this->getHaxeType($returnType))))));
			$i++;
		}
		$funcBeg = $i + 1;
		while($names[$funcBeg] === "T_WHITESPACE") {
			$funcBeg++;
		}
		if($values[$funcBeg] === "{") {
			$funcEnd = $this->getPairPos($names, $funcBeg);
			if($this->wantExtern) {
				stdlib_LambdaArray::spliceEx($names, $i + 1, $funcEnd - $i, (new _hx_array(array(";"))));
				stdlib_LambdaArray::spliceEx($values, $i + 1, $funcEnd - $i, (new _hx_array(array(";"))));
				$i = $funcBeg;
			} else {
				$this->processFunctionBody($names, $values, $vars, $funcBeg, $funcEnd);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $i;
		}
		$GLOBALS['%s']->pop();
	}
	public function processFunctionBody($names, $values, $vars, $funcBeg, $funcEnd) {
		$GLOBALS['%s']->push("PhpToHaxe::processFunctionBody");
		$__hx__spos = $GLOBALS['%s']->length;
		$i = $funcBeg;
		while($i < $funcEnd) {
			if($names[$i] === "T_VARIABLE" && $this->isAfterLexem($names, $i, (new _hx_array(array(";", "{", "}"))), 10) && !Lambda::has($vars, _hx_substr($values[$i], 1, null))) {
				$varNameIndex = $i;
				$i++;
				while($names[$i] === "T_WHITESPACE") {
					$i++;
				}
				if($values[$i] === "=") {
					$vars->push(_hx_substr($values[$varNameIndex], 1, null));
					$values[$varNameIndex] = "var " . _hx_string_or_null($values[$varNameIndex]);
				}
				unset($varNameIndex);
			}
			$i++;
		}
		$GLOBALS['%s']->pop();
	}
	public function getVarTypesByDocComment($comment) {
		$GLOBALS['%s']->push("PhpToHaxe::getVarTypesByDocComment");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = null;
		{
			$this1 = null;
			$this1 = array();
			$r = $this1;
		}
		$matches = null;
		if(PhpToHaxe_3($this, $comment, $matches, $r) > 0) {
			$_g = 0;
			while($_g < count($matches)) {
				$m = $matches[$_g];
				++$_g;
				$r[$m["name"]] = $m["type"];
				unset($m);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	public function getReturnTypesByDocComment($comment) {
		$GLOBALS['%s']->push("PhpToHaxe::getReturnTypesByDocComment");
		$__hx__spos = $GLOBALS['%s']->length;
		$m = null;
		if(preg_match("/@return\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)/", $comment, $m, 0, 0) > 0) {
			$tmp = $m["type"];
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$GLOBALS['%s']->pop();
			return "";
		}
		$GLOBALS['%s']->pop();
	}
	public function getHaxeType($phpType) {
		$GLOBALS['%s']->push("PhpToHaxe::getHaxeType");
		$__hx__spos = $GLOBALS['%s']->length;
		if(PhpToHaxe_4($this, $phpType)) {
			$tmp = $this->typeNamesMapping->get($phpType);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$GLOBALS['%s']->pop();
			return $phpType;
		}
		$GLOBALS['%s']->pop();
	}
	public function processDocComment($names, $values, $i) {
		$GLOBALS['%s']->push("PhpToHaxe::processDocComment");
		$__hx__spos = $GLOBALS['%s']->length;
		$comment = $values[$i];
		$comment = php_PcreNatives::preg_replace("/(@param\\s+)[_a-zA-Z][_a-zA-Z0-9]*\\s+[\\\$]([_a-zA-Z0-9]*)/", "\\1\\2", $comment, null, null);
		$comment = php_PcreNatives::preg_replace("/^\\s*[*]\\s*@param\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*[\x0D\x0A]+/m", "", $comment, null, null);
		$comment = php_PcreNatives::preg_replace("/^\\s*[*]\\s*@return\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*[\x0D\x0A]+/m", "", $comment, null, null);
		$comment = php_PcreNatives::preg_replace("/^\\s*[*]\\s*[\x0D\x0A]+/m", "", $comment, null, null);
		$values[$i] = $comment;
		$GLOBALS['%s']->pop();
	}
	public function detectVarType($names, $values, $i) {
		$GLOBALS['%s']->push("PhpToHaxe::detectVarType");
		$__hx__spos = $GLOBALS['%s']->length;
		$n = $i - 1;
		while($n > 0 && Lambda::has((new _hx_array(array("T_WHITESPACE", "T_PUBLIC", "T_PRIVATE", "T_CONST", "T_STATIC"))), $names[$n])) {
			$n--;
		}
		if($n < 0 || $names[$n] !== "T_DOC_COMMENT") {
			$GLOBALS['%s']->pop();
			return "";
		}
		$comment = $values[$n];
		$m = null;
		if(preg_match("/@var\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)/", $comment, $m, 0, 0) > 0) {
			$comment = php_PcreNatives::preg_replace("/^\\s*[*]?\\s*@var\\s+[_a-zA-Z][_a-zA-Z0-9]*\\s*\x0A/m", "", $comment, null, null);
			$comment = php_PcreNatives::preg_replace("/^\\s*[*]\\s*[\x0D\x0A]+/m", "", $comment, null, null);
			$values[$n] = $comment;
			{
				$tmp = $this->getHaxeType($m["type"]);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return "";
		}
		$GLOBALS['%s']->pop();
	}
	public function processVar($names, $values, $i) {
		$GLOBALS['%s']->push("PhpToHaxe::processVar");
		$__hx__spos = $GLOBALS['%s']->length;
		$prefix = null;
		if(StringTools::startsWith($values[$i], "var ")) {
			$prefix = "var ";
		} else {
			$prefix = "";
		}
		$values[$i] = _hx_substr($values[$i], strlen($prefix), null);
		if(StringTools::startsWith($values[$i], "\$")) {
			$values[$i] = _hx_substr($values[$i], 1, null);
		}
		$type = $this->detectVarType($names, $values, $i);
		if($type !== "") {
			$values[$i] = _hx_string_or_null($values[$i]) . " : " . _hx_string_or_null($type);
		}
		if(PhpToHaxe_5($this, $i, $names, $prefix, $type, $values)) {
			$values[$i] = $this->varNamesMapping->get($values[$i]);
		}
		if($i - 1 >= 0 && $names[$i - 1] === "T_ENCAPSED_AND_WHITESPACE") {
			$values[$i] = "\" + " . _hx_string_or_null($values[$i]);
		} else {
			if($i - 1 >= 0 && $names[$i - 1] === "\"") {
				$values[$i - 1] = "";
			}
		}
		if($i + 1 < $names->length && $names[$i + 1] === "T_ENCAPSED_AND_WHITESPACE") {
			$values[$i] = _hx_string_or_null($values[$i]) . " + \"";
		} else {
			if($i + 1 < $names->length && $names[$i + 1] === "\"") {
				$values[$i + 1] = "";
			}
		}
		if($i + 1 < $names->length && $names[$i + 1] === "T_VARIABLE") {
			$values->a[$i] .= " + ";
		}
		if(!$this->isAfterLexem($names, $i, (new _hx_array(array("T_FUNCTION"))), 3) && $this->isAfterLexem($names, $i, (new _hx_array(array("T_PUBLIC", "T_PRIVATE", "T_STATIC"))), 3)) {
			$values[$i] = "var " . _hx_string_or_null($values[$i]);
		}
		$values[$i] = _hx_string_or_null($prefix) . _hx_string_or_null($values[$i]);
		$GLOBALS['%s']->pop();
	}
	public function processFunctionCall($names, $values, $i) {
		$GLOBALS['%s']->push("PhpToHaxe::processFunctionCall");
		$__hx__spos = $GLOBALS['%s']->length;
		if(PhpToHaxe_6($this, $i, $names, $values)) {
			$rval = $this->functionNameMapping->get($values[$i]);
			$newFuncName = null;
			if(Std::is($rval, _hx_qtype("Array"))) {
				$newFuncName = $rval[0];
			} else {
				$newFuncName = $rval;
			}
			$values[$i] = $newFuncName;
			if(Std::is($rval, _hx_qtype("Array"))) {
				$rval1 = $rval;
				if($i + 1 < $names->length && $names[$i + 1] === "(") {
					$n = $this->getPairPos($names, $i + 1);
					$params = $this->splitTokensByComma($names->slice($i + 2, $n), $values->slice($i + 2, $n));
					$insertNames = (new _hx_array(array()));
					$insertValues = (new _hx_array(array()));
					$j = 0;
					while($j < $rval1->length) {
						$param = $rval1[$j];
						if(is_string($param)) {
							$insertNames->push(((_hx_index_of("([{}])", $param, null) >= 0) ? $param : "_CORRECTED"));
							$insertValues->push($param);
						} else {
							if(isset($params[$param])) {
								$killSkobki = $j > 0 && _hx_equal($rval1[$j - 1], "(") && $j + 1 < $rval1->length && _hx_equal($rval1[$j + 1], ")") && $j + 2 < $rval1->length && _hx_equal($rval1[$j + 2], ".") && $this->isSolidExpression(_hx_array_get($params, $param)->names);
								if($killSkobki) {
									$insertNames->pop();
									$insertValues->pop();
								}
								$insertNames = $insertNames->concat(_hx_array_get($params, $param)->names);
								$insertValues = $insertValues->concat(_hx_array_get($params, $param)->values);
								if($killSkobki) {
									$j++;
								}
								unset($killSkobki);
							} else {
								if($j + 2 === $rval1->length && _hx_equal($rval1[$j + 1], ")") && $j > 1 && stdlib_StringTools::rtrim($rval1[$j - 1], null) === ",") {
									$insertNames->pop();
									$insertValues->pop();
								}
							}
						}
						$j++;
						unset($param);
					}
					stdlib_LambdaArray::spliceEx($names, $i, $n - $i + 1, $insertNames);
					stdlib_LambdaArray::spliceEx($values, $i, $n - $i + 1, $insertValues);
					$i--;
				}
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $i;
		}
		$GLOBALS['%s']->pop();
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'PhpToHaxe'; }
}
function PhpToHaxe_0(&$__hx__this, &$_g, &$names, &$text, &$token, &$tokens, &$values) {
	{
		$token1 = $token[0];
		return token_name($token1);
	}
}
function PhpToHaxe_1(&$__hx__this, &$_g, &$begParamsIndex, &$commentIndex, &$commentVarTypes, &$defVal, &$endParamsIndex, &$i, &$methodName, &$n, &$name, &$names, &$param, &$paramNames, &$paramValues, &$params, &$phpEmptyArray, &$resParamsStr, &$returnType, &$type, &$values, &$vars) {
	if($type !== "") {
		return ":" . _hx_string_or_null($__hx__this->getHaxeType($type));
	} else {
		return "";
	}
}
function PhpToHaxe_2(&$__hx__this, &$_g, &$begParamsIndex, &$commentIndex, &$commentVarTypes, &$defVal, &$endParamsIndex, &$i, &$methodName, &$n, &$name, &$names, &$param, &$paramNames, &$paramValues, &$params, &$phpEmptyArray, &$resParamsStr, &$returnType, &$type, &$values, &$vars) {
	if($defVal !== "") {
		return "=" . _hx_string_or_null($defVal);
	} else {
		return "";
	}
}
function PhpToHaxe_3(&$__hx__this, &$comment, &$matches, &$r) {
	{
		$flags = PREG_SET_ORDER;
		return preg_match_all("/@param\\s+(?<type>[_a-zA-Z][_a-zA-Z0-9]*)\\s+[\\\$]?(?<name>[_a-zA-Z][_a-zA-Z0-9]*)/", $comment, $matches, ((($flags === null)) ? PREG_PATTERN_ORDER : $flags), 0);
	}
}
function PhpToHaxe_4(&$__hx__this, &$phpType) {
	{
		$var_ = $__hx__this->typeNamesMapping->get($phpType);
		return isset($var_);
	}
}
function PhpToHaxe_5(&$__hx__this, &$i, &$names, &$prefix, &$type, &$values) {
	{
		$var_ = $__hx__this->varNamesMapping->get($values[$i]);
		return isset($var_);
	}
}
function PhpToHaxe_6(&$__hx__this, &$i, &$names, &$values) {
	{
		$var_ = $__hx__this->functionNameMapping->get($values[$i]);
		return isset($var_);
	}
}
