<?php

class stdlib_Debug {
	public function __construct(){}
	static function getDump($v, $limit = null, $level = null, $prefix = null) {
		$GLOBALS['%s']->push("stdlib.Debug::getDump");
		$__hx__spos = $GLOBALS['%s']->length;
		if($prefix === null) {
			$prefix = "";
		}
		if($level === null) {
			$level = 0;
		}
		if($limit === null) {
			$limit = 10;
		}
		if($level >= $limit) {
			$GLOBALS['%s']->pop();
			return "...\x0A";
		}
		$prefix .= "\x09";
		$s = "?\x0A";
		{
			$_g = Type::typeof($v);
			switch($_g->index) {
			case 3:{
				$s = "BOOL(" . _hx_string_or_null(((($v) ? "true" : "false"))) . ")\x0A";
			}break;
			case 0:{
				$s = "NULL\x0A";
			}break;
			case 6:{
				$c = _hx_deref($_g)->params[0];
				if((is_object($_t = $c) && !($_t instanceof Enum) ? $_t === _hx_qtype("String") : $_t == _hx_qtype("String"))) {
					$s = "STRING(" . Std::string($v) . ")\x0A";
				} else {
					if((is_object($_t2 = $c) && !($_t2 instanceof Enum) ? $_t2 === _hx_qtype("Array") : $_t2 == _hx_qtype("Array"))) {
						$s = "ARRAY(" . Std::string(_hx_len($v)) . ")\x0A";
						{
							$_g1 = 0;
							$_g2 = null;
							$_g2 = $v;
							while($_g1 < $_g2->length) {
								$item = $_g2[$_g1];
								++$_g1;
								$s .= _hx_string_or_null($prefix) . _hx_string_or_null(stdlib_Debug::getDump($item, $limit, $level + 1, $prefix));
								unset($item);
							}
						}
					} else {
						if((is_object($_t3 = $c) && !($_t3 instanceof Enum) ? $_t3 === _hx_qtype("List") : $_t3 == _hx_qtype("List"))) {
							$s = "LIST(" . _hx_string_rec(Lambda::count($v, null), "") . ")\x0A";
							if(null == ($v)) throw new HException('null iterable');
							$__hx__it = _hx_deref(($v))->iterator();
							while($__hx__it->hasNext()) {
								unset($item1);
								$item1 = $__hx__it->next();
								$s .= _hx_string_or_null($prefix) . _hx_string_or_null(stdlib_Debug::getDump($item1, $limit, $level + 1, $prefix));
							}
						} else {
							if((is_object($_t4 = $c) && !($_t4 instanceof Enum) ? $_t4 === _hx_qtype("haxe.ds.StringMap") : $_t4 == _hx_qtype("haxe.ds.StringMap"))) {
								$s = "StringMap\x0A";
								$map = null;
								$map = $v;
								if(null == $map) throw new HException('null iterable');
								$__hx__it = $map->keys();
								while($__hx__it->hasNext()) {
									unset($key);
									$key = $__hx__it->next();
									$s .= _hx_string_or_null($prefix) . _hx_string_or_null($key) . " => " . _hx_string_or_null(stdlib_Debug::getDump($map->get($key), $limit, $level + 1, $prefix));
								}
							} else {
								$s = "CLASS(" . _hx_string_or_null(Type::getClassName($c)) . ")\x0A" . _hx_string_or_null(stdlib_Debug::getObjectDump($v, $limit, $level + 1, $prefix));
							}
						}
					}
				}
			}break;
			case 7:{
				$e = _hx_deref($_g)->params[0];
				$s = "ENUM(" . _hx_string_or_null(Type::getEnumName($e)) . ") = " . _hx_string_or_null(Type::enumConstructor($v)) . "\x0A";
			}break;
			case 2:{
				$s = "FLOAT(" . Std::string($v) . ")\x0A";
			}break;
			case 1:{
				$s = "INT(" . Std::string($v) . ")\x0A";
			}break;
			case 4:{
				$s = "OBJECT" . "\x0A" . _hx_string_or_null(stdlib_Debug::getObjectDump($v, $limit, $level + 1, $prefix));
			}break;
			case 5:case 8:{
				$s = "FUNCTION OR UNKNOW\x0A";
			}break;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $s;
		}
		$GLOBALS['%s']->pop();
	}
	static function getObjectDump($obj, $limit, $level, $prefix) {
		$GLOBALS['%s']->push("stdlib.Debug::getObjectDump");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = "";
		{
			$_g = 0;
			$_g1 = Reflect::fields($obj);
			while($_g < $_g1->length) {
				$fieldName = $_g1[$_g];
				++$_g;
				$s .= _hx_string_or_null($prefix) . _hx_string_or_null($fieldName) . " : " . _hx_string_or_null(stdlib_Debug::getDump(Reflect::field($obj, $fieldName), $limit, $level, $prefix));
				unset($fieldName);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $s;
		}
		$GLOBALS['%s']->pop();
	}
	static function assert($e, $message = null, $pos = null) {
		$GLOBALS['%s']->push("stdlib.Debug::assert");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!$e) {
			if($message === null) {
				$message = "error";
			} else {
				if(Reflect::isFunction($message)) {
					$message = call_user_func($message);
				}
			}
			$s = "ASSERT " . Std::string($message) . " in " . _hx_string_or_null($pos->fileName) . " at line " . _hx_string_rec($pos->lineNumber, "");
			$r = new stdlib_Exception($s);
			$r->stack->shift();
			throw new HException($r);
		}
		$GLOBALS['%s']->pop();
	}
	static function traceStack($v, $pos = null) {
		$GLOBALS['%s']->push("stdlib.Debug::traceStack");
		$__hx__spos = $GLOBALS['%s']->length;
		$stack = stdlib_StringTools::trim(stdlib_Debug_0($pos, $v), null);
		haxe_Log::trace("TRACE " . _hx_string_or_null((((Std::is($v, _hx_qtype("String"))) ? $v : stdlib_StringTools::trim(stdlib_Debug::getDump($v, null, null, null), null)))) . "\x0AStack trace:\x0A" . _hx_string_or_null($stack), _hx_anonymous(array("fileName" => "Debug.hx", "lineNumber" => 136, "className" => "stdlib.Debug", "methodName" => "traceStack", "customParams" => (new _hx_array(array($pos))))));
		$GLOBALS['%s']->pop();
	}
	static function methodMustBeOverriden($_this, $pos = null) {
		$GLOBALS['%s']->push("stdlib.Debug::methodMustBeOverriden");
		$__hx__spos = $GLOBALS['%s']->length;
		throw new HException(new stdlib_Exception("Method " . _hx_string_or_null($pos->methodName) . "() must be overriden in class " . _hx_string_or_null(Type::getClassName(Type::getClass($_this))) . "."));
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	static function methodNotSupported($_this, $pos = null) {
		$GLOBALS['%s']->push("stdlib.Debug::methodNotSupported");
		$__hx__spos = $GLOBALS['%s']->length;
		throw new HException(new stdlib_Exception("Method " . _hx_string_or_null($pos->methodName) . "() is not supported by class " . _hx_string_or_null(Type::getClassName(Type::getClass($_this))) . "."));
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'stdlib.Debug'; }
}
function stdlib_Debug_0(&$pos, &$v) {
	{
		$s = haxe_CallStack::toString(haxe_CallStack::callStack());
		return str_replace("prototype<.", "", $s);
	}
}
