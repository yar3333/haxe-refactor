<?php

class Std {
	public function __construct(){}
	static function is($v, $t) {
		$GLOBALS['%s']->push("Std::is");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_instanceof($v, $t);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function instance($value, $c) {
		$GLOBALS['%s']->push("Std::instance");
		$__hx__spos = $GLOBALS['%s']->length;
		if(Std::is($value, $c)) {
			$tmp = $value;
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	static function string($s) {
		$GLOBALS['%s']->push("Std::string");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_string_rec($s, "");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function int($x) {
		$GLOBALS['%s']->push("Std::int");
		$__hx__spos = $GLOBALS['%s']->length;
		$i = fmod($x, -2147483648) & -1;
		if($i & -2147483648) {
			$i = -((~$i & -1) + 1);
		}
		{
			$GLOBALS['%s']->pop();
			return $i;
		}
		$GLOBALS['%s']->pop();
	}
	static function parseInt($x) {
		$GLOBALS['%s']->push("Std::parseInt");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!is_numeric($x)) {
			$matches = null;
			preg_match("/^-?\\d+/", $x, $matches);
			if(count($matches) === 0) {
				$GLOBALS['%s']->pop();
				return null;
			} else {
				$tmp = intval($matches[0]);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		} else {
			if(strtolower(_hx_substr($x, 0, 2)) === "0x") {
				$tmp = (int) hexdec(substr($x, 2));
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$tmp = intval($x);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function parseFloat($x) {
		$GLOBALS['%s']->push("Std::parseFloat");
		$__hx__spos = $GLOBALS['%s']->length;
		$v = floatval($x);
		if($v === 0.0) {
			$x = rtrim($x);
			$v = floatval($x);
			if($v === 0.0 && !is_numeric($x)) {
				$v = acos(1.01);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $v;
		}
		$GLOBALS['%s']->pop();
	}
	static function random($x) {
		$GLOBALS['%s']->push("Std::random");
		$__hx__spos = $GLOBALS['%s']->length;
		if($x <= 0) {
			$GLOBALS['%s']->pop();
			return 0;
		} else {
			$tmp = mt_rand(0, $x - 1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Std'; }
}
