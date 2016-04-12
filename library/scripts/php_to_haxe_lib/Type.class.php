<?php

class Type {
	public function __construct(){}
	static function getClass($o) {
		$GLOBALS['%s']->push("Type::getClass");
		$__hx__spos = $GLOBALS['%s']->length;
		if($o === null) {
			$GLOBALS['%s']->pop();
			return null;
		}
		if(is_array($o)) {
			if(count($o) === 2 && is_callable($o)) {
				$GLOBALS['%s']->pop();
				return null;
			}
			{
				$tmp = _hx_ttype("Array");
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		if(is_string($o)) {
			if(_hx_is_lambda($o)) {
				$GLOBALS['%s']->pop();
				return null;
			}
			{
				$tmp = _hx_ttype("String");
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		if(!is_object($o)) {
			$GLOBALS['%s']->pop();
			return null;
		}
		$c = get_class($o);
		if($c === false || $c === "_hx_anonymous" || is_subclass_of($c, "enum")) {
			$GLOBALS['%s']->pop();
			return null;
		} else {
			$tmp = _hx_ttype($c);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function getClassName($c) {
		$GLOBALS['%s']->push("Type::getClassName");
		$__hx__spos = $GLOBALS['%s']->length;
		if($c === null) {
			$GLOBALS['%s']->pop();
			return null;
		}
		{
			$tmp = $c->__qname__;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function getEnumName($e) {
		$GLOBALS['%s']->push("Type::getEnumName");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $e->__qname__;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function resolveClass($name) {
		$GLOBALS['%s']->push("Type::resolveClass");
		$__hx__spos = $GLOBALS['%s']->length;
		$c = _hx_qtype($name);
		if($c instanceof _hx_class || $c instanceof _hx_interface) {
			$GLOBALS['%s']->pop();
			return $c;
		} else {
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	static function createEmptyInstance($cl) {
		$GLOBALS['%s']->push("Type::createEmptyInstance");
		$__hx__spos = $GLOBALS['%s']->length;
		if($cl->__qname__ === "Array") {
			$tmp = (new _hx_array(array()));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($cl->__qname__ === "String") {
			$GLOBALS['%s']->pop();
			return "";
		}
		try {
			php_Boot::$skip_constructor = true;
			$rfl = $cl->__rfl__();
			if($rfl === null) {
				$GLOBALS['%s']->pop();
				return null;
			}
			$m = $rfl->getConstructor();
			$nargs = $m->getNumberOfRequiredParameters();
			$i = null;
			if($nargs > 0) {
				$args = array_fill(0, $m->getNumberOfRequiredParameters(), null);
				$i = $rfl->newInstanceArgs($args);
			} else {
				$i = $rfl->newInstanceArgs(array());
			}
			php_Boot::$skip_constructor = false;
			{
				$GLOBALS['%s']->pop();
				return $i;
			}
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			$e = $_ex_;
			{
				$GLOBALS['%e'] = (new _hx_array(array()));
				while($GLOBALS['%s']->length >= $__hx__spos) {
					$GLOBALS['%e']->unshift($GLOBALS['%s']->pop());
				}
				$GLOBALS['%s']->push($GLOBALS['%e'][0]);
				php_Boot::$skip_constructor = false;
				throw new HException("Unable to instantiate " . Std::string($cl));
			}
		}
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	static function getInstanceFields($c) {
		$GLOBALS['%s']->push("Type::getInstanceFields");
		$__hx__spos = $GLOBALS['%s']->length;
		if($c->__qname__ === "String") {
			$tmp = (new _hx_array(array("substr", "charAt", "charCodeAt", "indexOf", "lastIndexOf", "split", "toLowerCase", "toUpperCase", "toString", "length")));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($c->__qname__ === "Array") {
			$tmp = (new _hx_array(array("push", "concat", "join", "pop", "reverse", "shift", "slice", "sort", "splice", "toString", "copy", "unshift", "insert", "remove", "iterator", "length")));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		
		$rfl = $c->__rfl__();
		if($rfl === null) return new _hx_array(array());
		$r = array();
		$internals = array('__construct', '__call', '__get', '__set', '__isset', '__unset', '__toString');
		$ms = $rfl->getMethods();
		while(list(, $m) = each($ms)) {
			$n = $m->getName();
			if(!$m->isStatic() && !in_array($n, $internals)) $r[] = $n;
		}
		$ps = $rfl->getProperties();
		while(list(, $p) = each($ps))
			if(!$p->isStatic() && ($name = $p->getName()) !== '__dynamics') $r[] = $name;
		;
		{
			$tmp = new _hx_array(array_values(array_unique($r)));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function typeof($v) {
		$GLOBALS['%s']->push("Type::typeof");
		$__hx__spos = $GLOBALS['%s']->length;
		if($v === null) {
			$tmp = ValueType::$TNull;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if(is_array($v)) {
			if(is_callable($v)) {
				$tmp = ValueType::$TFunction;
				$GLOBALS['%s']->pop();
				return $tmp;
			}
			{
				$tmp = ValueType::TClass(_hx_qtype("Array"));
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		if(is_string($v)) {
			if(_hx_is_lambda($v)) {
				$tmp = ValueType::$TFunction;
				$GLOBALS['%s']->pop();
				return $tmp;
			}
			{
				$tmp = ValueType::TClass(_hx_qtype("String"));
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		if(is_bool($v)) {
			$tmp = ValueType::$TBool;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if(is_int($v)) {
			$tmp = ValueType::$TInt;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if(is_float($v)) {
			$tmp = ValueType::$TFloat;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($v instanceof _hx_anonymous) {
			$tmp = ValueType::$TObject;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($v instanceof _hx_enum) {
			$tmp = ValueType::$TObject;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($v instanceof _hx_class) {
			$tmp = ValueType::$TObject;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$c = _hx_ttype(get_class($v));
		if($c instanceof _hx_enum) {
			$tmp = ValueType::TEnum($c);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($c instanceof _hx_class) {
			$tmp = ValueType::TClass($c);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$tmp = ValueType::$TUnknown;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function enumConstructor($e) {
		$GLOBALS['%s']->push("Type::enumConstructor");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $e->tag;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Type'; }
}
