<?php

class stdlib_Std {
	public function __construct(){}
	static function parseInt($x, $defaultValue = null) {
		$GLOBALS['%s']->push("stdlib.Std::parseInt");
		$__hx__spos = $GLOBALS['%s']->length;
		if($x !== null) {
			if(_hx_deref(new EReg("^\\s*[+-]?\\s*((?:0x[0-9a-fA-F]{1,7})|(?:\\d{1,9}))\\s*\$", ""))->match($x)) {
				$tmp = Std::parseInt($x);
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$GLOBALS['%s']->pop();
				return $defaultValue;
			}
		} else {
			$GLOBALS['%s']->pop();
			return $defaultValue;
		}
		$GLOBALS['%s']->pop();
	}
	static function parseFloat($x, $defaultValue = null) {
		$GLOBALS['%s']->push("stdlib.Std::parseFloat");
		$__hx__spos = $GLOBALS['%s']->length;
		if($x === null) {
			$GLOBALS['%s']->pop();
			return $defaultValue;
		}
		if(_hx_deref(new EReg("^\\s*[+-]?\\s*\\d{1,20}(?:[.]\\d+)?(?:e[+-]?\\d{1,20})?\\s*\$", ""))->match($x)) {
			$r = Std::parseFloat($x);
			if(!Math::isNaN($r)) {
				$GLOBALS['%s']->pop();
				return $r;
			} else {
				$GLOBALS['%s']->pop();
				return $defaultValue;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $defaultValue;
		}
		$GLOBALS['%s']->pop();
	}
	static function bool($v) {
		$GLOBALS['%s']->push("stdlib.Std::bool");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = !_hx_equal($v, false) && $v !== null && !_hx_equal($v, 0) && !_hx_equal($v, "") && !_hx_equal($v, "0") && (!Std::is($v, _hx_qtype("String")) || strtolower(($v)) !== "false" && strtolower(($v)) !== "off" && strtolower(($v)) !== "null");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function parseValue($x) {
		$GLOBALS['%s']->push("stdlib.Std::parseValue");
		$__hx__spos = $GLOBALS['%s']->length;
		$value = $x;
		$valueLC = null;
		if($value !== null) {
			$valueLC = _hx_string_call($value, "toLowerCase", array());
		} else {
			$valueLC = null;
		}
		$parsedValue = null;
		if($valueLC === "true") {
			$value = true;
		} else {
			if($valueLC === "false") {
				$value = false;
			} else {
				if($valueLC === "null") {
					$value = null;
				} else {
					if(($parsedValue = stdlib_Std::parseInt($value, null)) !== null) {
						$value = $parsedValue;
					} else {
						if(($parsedValue = stdlib_Std::parseFloat($value, null)) !== null) {
							$value = $parsedValue;
						}
					}
				}
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $value;
		}
		$GLOBALS['%s']->pop();
	}
	static function hash($obj) {
		$GLOBALS['%s']->push("stdlib.Std::hash");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new haxe_ds_StringMap();
		{
			$_g = 0;
			$_g1 = Reflect::fields($obj);
			while($_g < $_g1->length) {
				$key = $_g1[$_g];
				++$_g;
				{
					$value = Reflect::field($obj, $key);
					$r->set($key, $value);
					unset($value);
				}
				unset($key);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function min($a, $b) {
		$GLOBALS['%s']->push("stdlib.Std::min");
		$__hx__spos = $GLOBALS['%s']->length;
		if($a < $b) {
			$GLOBALS['%s']->pop();
			return $a;
		} else {
			$GLOBALS['%s']->pop();
			return $b;
		}
		$GLOBALS['%s']->pop();
	}
	static function max($a, $b) {
		$GLOBALS['%s']->push("stdlib.Std::max");
		$__hx__spos = $GLOBALS['%s']->length;
		if($a > $b) {
			$GLOBALS['%s']->pop();
			return $a;
		} else {
			$GLOBALS['%s']->pop();
			return $b;
		}
		$GLOBALS['%s']->pop();
	}
	static function abs($x) {
		$GLOBALS['%s']->push("stdlib.Std::abs");
		$__hx__spos = $GLOBALS['%s']->length;
		if($x >= 0) {
			$GLOBALS['%s']->pop();
			return $x;
		} else {
			$tmp = -$x;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function sign($n) {
		$GLOBALS['%s']->push("stdlib.Std::sign");
		$__hx__spos = $GLOBALS['%s']->length;
		if($n > 0) {
			$GLOBALS['%s']->pop();
			return 1;
		} else {
			if($n < 0) {
				$GLOBALS['%s']->pop();
				return -1;
			} else {
				$GLOBALS['%s']->pop();
				return 0;
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function is($v, $t) {
		$GLOBALS['%s']->push("stdlib.Std::is");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Std::is($v, $t);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function instance($value, $c) {
		$GLOBALS['%s']->push("stdlib.Std::instance");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Std::instance($value, $c);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function string($s) {
		$GLOBALS['%s']->push("stdlib.Std::string");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Std::string($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function int($x) {
		$GLOBALS['%s']->push("stdlib.Std::int");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Std::int($x);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function random($x) {
		$GLOBALS['%s']->push("stdlib.Std::random");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Std::random($x);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'stdlib.Std'; }
}
