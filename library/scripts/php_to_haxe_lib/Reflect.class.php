<?php

class Reflect {
	public function __construct(){}
	static function field($o, $field) {
		$GLOBALS['%s']->push("Reflect::field");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_field($o, $field);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function fields($o) {
		$GLOBALS['%s']->push("Reflect::fields");
		$__hx__spos = $GLOBALS['%s']->length;
		if($o === null) {
			$tmp = new _hx_array(array());
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($o instanceof _hx_array) {
			$tmp = new _hx_array(array('concat','copy','insert','iterator','length','join','pop','push','remove','reverse','shift','slice','sort','splice','toString','unshift'));
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			if(is_string($o)) {
				$tmp = new _hx_array(array('charAt','charCodeAt','indexOf','lastIndexOf','length','split','substr','toLowerCase','toString','toUpperCase'));
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$tmp = new _hx_array(_hx_get_object_vars($o));
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function isFunction($f) {
		$GLOBALS['%s']->push("Reflect::isFunction");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = (is_array($f) && is_callable($f)) || _hx_is_lambda($f) || is_array($f) && Reflect_0($f) && $f[1] !== "length";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function compare($a, $b) {
		$GLOBALS['%s']->push("Reflect::compare");
		$__hx__spos = $GLOBALS['%s']->length;
		if((is_object($_t = $a) && !($_t instanceof Enum) ? $_t === $b : $_t == $b)) {
			$GLOBALS['%s']->pop();
			return 0;
		} else {
			if(is_string($a)) {
				$tmp = strcmp($a, $b);
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				if($a > $b) {
					$GLOBALS['%s']->pop();
					return 1;
				} else {
					$GLOBALS['%s']->pop();
					return -1;
				}
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function isObject($v) {
		$GLOBALS['%s']->push("Reflect::isObject");
		$__hx__spos = $GLOBALS['%s']->length;
		if($v === null) {
			$GLOBALS['%s']->pop();
			return false;
		}
		if(is_object($v)) {
			$tmp = $v instanceof _hx_anonymous || Type::getClass($v) !== null;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$tmp = is_string($v) && !_hx_is_lambda($v);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Reflect'; }
}
function Reflect_0(&$f) {
	{
		$o = $f[0];
		$field = $f[1];
		return _hx_has_field($o, $field);
	}
}
