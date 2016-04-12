<?php

class tjson_TJSONParser {
	public function __construct($vjson, $vfileName = null, $stringProcessor = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("tjson.TJSONParser::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($vfileName === null) {
			$vfileName = "JSON Data";
		}
		$this->json = $vjson;
		$this->fileName = $vfileName;
		$this->currentLine = 1;
		$this->lastSymbolQuoted = false;
		$this->pos = 0;
		$this->floatRegex = new EReg("^-?[0-9]*\\.[0-9]+\$", "");
		$this->intRegex = new EReg("^-?[0-9]+\$", "");
		if($stringProcessor === null) {
			$this->strProcessor = (isset($this->defaultStringProcessor) ? $this->defaultStringProcessor: array($this, "defaultStringProcessor"));
		} else {
			$this->strProcessor = $stringProcessor;
		}
		$this->cache = new _hx_array(array());
		$GLOBALS['%s']->pop();
	}}
	public $pos;
	public $json;
	public $lastSymbolQuoted;
	public $fileName;
	public $currentLine;
	public $cache;
	public $floatRegex;
	public $intRegex;
	public $strProcessor;
	public function doParse() {
		$GLOBALS['%s']->push("tjson.TJSONParser::doParse");
		$__hx__spos = $GLOBALS['%s']->length;
		try {
			{
				$_g = $this->getNextSymbol();
				{
					$s = $_g;
					switch($_g) {
					case "{":{
						$tmp = $this->doObject();
						$GLOBALS['%s']->pop();
						return $tmp;
					}break;
					case "[":{
						$tmp = $this->doArray();
						$GLOBALS['%s']->pop();
						return $tmp;
					}break;
					default:{
						$tmp = $this->convertSymbolToProperType($s);
						$GLOBALS['%s']->pop();
						return $tmp;
					}break;
					}
				}
			}
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			if(is_string($e = $_ex_)){
				$GLOBALS['%e'] = (new _hx_array(array()));
				while($GLOBALS['%s']->length >= $__hx__spos) {
					$GLOBALS['%e']->unshift($GLOBALS['%s']->pop());
				}
				$GLOBALS['%s']->push($GLOBALS['%e'][0]);
				throw new HException(_hx_string_or_null($this->fileName) . " on line " . _hx_string_rec($this->currentLine, "") . ": " . _hx_string_or_null($e));
			} else throw $__hx__e;;
		}
		$GLOBALS['%s']->pop();
	}
	public function doObject() {
		$GLOBALS['%s']->push("tjson.TJSONParser::doObject");
		$__hx__spos = $GLOBALS['%s']->length;
		$o = _hx_anonymous(array());
		$val = "";
		$key = null;
		$isClassOb = false;
		$this->cache->push($o);
		while($this->pos < strlen($this->json)) {
			$key = $this->getNextSymbol();
			if($key === "," && !$this->lastSymbolQuoted) {
				continue;
			}
			if($key === "}" && !$this->lastSymbolQuoted) {
				if($isClassOb && _hx_field($o, "TJ_unserialize") !== null) {
					$o->TJ_unserialize();
				}
				{
					$GLOBALS['%s']->pop();
					return $o;
				}
			}
			$seperator = $this->getNextSymbol();
			if($seperator !== ":") {
				throw new HException("Expected ':' but got '" . _hx_string_or_null($seperator) . "' instead.");
			}
			$v = $this->getNextSymbol();
			if($key === "_hxcls") {
				$cls = Type::resolveClass($v);
				if($cls === null) {
					throw new HException("Invalid class name - " . _hx_string_or_null($v));
				}
				$o = Type::createEmptyInstance($cls);
				$this->cache->pop();
				$this->cache->push($o);
				$isClassOb = true;
				continue;
				unset($cls);
			}
			if($v === "{" && !$this->lastSymbolQuoted) {
				$val = $this->doObject();
			} else {
				if($v === "[" && !$this->lastSymbolQuoted) {
					$val = $this->doArray();
				} else {
					$val = $this->convertSymbolToProperType($v);
				}
			}
			$o->{$key} = $val;
			unset($v,$seperator);
		}
		throw new HException("Unexpected end of file. Expected '}'");
		$GLOBALS['%s']->pop();
	}
	public function doArray() {
		$GLOBALS['%s']->push("tjson.TJSONParser::doArray");
		$__hx__spos = $GLOBALS['%s']->length;
		$a = new _hx_array(array());
		$val = null;
		while($this->pos < strlen($this->json)) {
			$val = $this->getNextSymbol();
			if(_hx_equal($val, ",") && !$this->lastSymbolQuoted) {
				continue;
			} else {
				if(_hx_equal($val, "]") && !$this->lastSymbolQuoted) {
					$GLOBALS['%s']->pop();
					return $a;
				} else {
					if(_hx_equal($val, "{") && !$this->lastSymbolQuoted) {
						$val = $this->doObject();
					} else {
						if(_hx_equal($val, "[") && !$this->lastSymbolQuoted) {
							$val = $this->doArray();
						} else {
							$val = $this->convertSymbolToProperType($val);
						}
					}
				}
			}
			$a->push($val);
		}
		throw new HException("Unexpected end of file. Expected ']'");
		$GLOBALS['%s']->pop();
	}
	public function convertSymbolToProperType($symbol) {
		$GLOBALS['%s']->push("tjson.TJSONParser::convertSymbolToProperType");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->lastSymbolQuoted) {
			if(StringTools::startsWith($symbol, tjson_TJSON::$OBJECT_REFERENCE_PREFIX)) {
				$idx = Std::parseInt(_hx_substr($symbol, strlen(tjson_TJSON::$OBJECT_REFERENCE_PREFIX), null));
				{
					$tmp = $this->cache[$idx];
					$GLOBALS['%s']->pop();
					return $tmp;
				}
			}
			{
				$GLOBALS['%s']->pop();
				return $symbol;
			}
		}
		if($this->looksLikeFloat($symbol)) {
			$tmp = Std::parseFloat($symbol);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($this->looksLikeInt($symbol)) {
			$tmp = Std::parseInt($symbol);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if(strtolower($symbol) === "true") {
			$GLOBALS['%s']->pop();
			return true;
		}
		if(strtolower($symbol) === "false") {
			$GLOBALS['%s']->pop();
			return false;
		}
		if(strtolower($symbol) === "null") {
			$GLOBALS['%s']->pop();
			return null;
		}
		{
			$GLOBALS['%s']->pop();
			return $symbol;
		}
		$GLOBALS['%s']->pop();
	}
	public function looksLikeFloat($s) {
		$GLOBALS['%s']->push("tjson.TJSONParser::looksLikeFloat");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->floatRegex->match($s) || $this->intRegex->match($s) && tjson_TJSONParser_0($this, $s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function looksLikeInt($s) {
		$GLOBALS['%s']->push("tjson.TJSONParser::looksLikeInt");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->intRegex->match($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getNextSymbol() {
		$GLOBALS['%s']->push("tjson.TJSONParser::getNextSymbol");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->lastSymbolQuoted = false;
		$c = "";
		$inQuote = false;
		$quoteType = "";
		$symbol = "";
		$inEscape = false;
		$inSymbol = false;
		$inLineComment = false;
		$inBlockComment = false;
		while($this->pos < strlen($this->json)) {
			$c = _hx_char_at($this->json, $this->pos++);
			if($c === "\x0A" && !$inSymbol) {
				$this->currentLine++;
			}
			if($inLineComment) {
				if($c === "\x0A" || $c === "\x0D") {
					$inLineComment = false;
					$this->pos++;
				}
				continue;
			}
			if($inBlockComment) {
				if($c === "*" && _hx_char_at($this->json, $this->pos) === "/") {
					$inBlockComment = false;
					$this->pos++;
				}
				continue;
			}
			if($inQuote) {
				if($inEscape) {
					$inEscape = false;
					if($c === "'" || $c === "\"") {
						$symbol .= _hx_string_or_null($c);
						continue;
					}
					if($c === "t") {
						$symbol .= "\x09";
						continue;
					}
					if($c === "n") {
						$symbol .= "\x0A";
						continue;
					}
					if($c === "\\") {
						$symbol .= "\\";
						continue;
					}
					if($c === "r") {
						$symbol .= "\x0D";
						continue;
					}
					if($c === "/") {
						$symbol .= "/";
						continue;
					}
					if($c === "u") {
						$hexValue = 0;
						{
							$_g = 0;
							while($_g < 4) {
								$i = $_g++;
								if($this->pos >= strlen($this->json)) {
									throw new HException("Unfinished UTF8 character");
								}
								$nc = _hx_char_code_at($this->json, $this->pos++);
								$hexValue = $hexValue << 4;
								if($nc >= 48 && $nc <= 57) {
									$hexValue += $nc - 48;
								} else {
									if($nc >= 65 && $nc <= 70) {
										$hexValue += 10 + $nc - 65;
									} else {
										if($nc >= 97 && $nc <= 102) {
											$hexValue += 10 + $nc - 95;
										} else {
											throw new HException("Not a hex digit");
										}
									}
								}
								unset($nc,$i);
							}
							unset($_g);
						}
						$utf = new haxe_Utf8(null);
						$utf->addChar($hexValue);
						$symbol .= _hx_string_or_null($utf->toString());
						continue;
						unset($utf,$hexValue);
					}
					throw new HException("Invalid escape sequence '\\" . _hx_string_or_null($c) . "'");
				} else {
					if($c === "\\") {
						$inEscape = true;
						continue;
					}
					if($c === $quoteType) {
						$GLOBALS['%s']->pop();
						return $symbol;
					}
					$symbol .= _hx_string_or_null($c);
					continue;
				}
			} else {
				if($c === "/") {
					$c2 = _hx_char_at($this->json, $this->pos);
					if($c2 === "/") {
						$inLineComment = true;
						$this->pos++;
						continue;
					} else {
						if($c2 === "*") {
							$inBlockComment = true;
							$this->pos++;
							continue;
						}
					}
					unset($c2);
				}
			}
			if($inSymbol) {
				if($c === " " || $c === "\x0A" || $c === "\x0D" || $c === "\x09" || $c === "," || $c === ":" || $c === "}" || $c === "]") {
					$this->pos--;
					{
						$GLOBALS['%s']->pop();
						return $symbol;
					}
				} else {
					$symbol .= _hx_string_or_null($c);
					continue;
				}
			} else {
				if($c === " " || $c === "\x09" || $c === "\x0A" || $c === "\x0D") {
					continue;
				}
				if($c === "{" || $c === "}" || $c === "[" || $c === "]" || $c === "," || $c === ":") {
					$GLOBALS['%s']->pop();
					return $c;
				}
				if($c === "'" || $c === "\"") {
					$inQuote = true;
					$quoteType = $c;
					$this->lastSymbolQuoted = true;
					continue;
				} else {
					$inSymbol = true;
					$symbol = $c;
					continue;
				}
			}
		}
		if($inQuote) {
			throw new HException("Unexpected end of data. Expected ( " . _hx_string_or_null($quoteType) . " )");
		}
		{
			$GLOBALS['%s']->pop();
			return $symbol;
		}
		$GLOBALS['%s']->pop();
	}
	public function defaultStringProcessor($str) {
		$GLOBALS['%s']->push("tjson.TJSONParser::defaultStringProcessor");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return $str;
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
	function __toString() { return 'tjson.TJSONParser'; }
}
function tjson_TJSONParser_0(&$__hx__this, &$s) {
	{
		$intStr = $__hx__this->intRegex->matched(0);
		if(_hx_char_code_at($intStr, 0) === 45) {
			return (strcmp($intStr, "-2147483648")> 0);
		} else {
			return (strcmp($intStr, "2147483647")> 0);
		}
		unset($intStr);
	}
}
