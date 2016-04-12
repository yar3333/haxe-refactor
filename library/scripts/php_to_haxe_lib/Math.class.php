<?php

class Math {
	public function __construct(){}
	static $PI;
	static $NaN;
	static $POSITIVE_INFINITY;
	static $NEGATIVE_INFINITY;
	static function ceil($v) {
		$GLOBALS['%s']->push("Math::ceil");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = (int) ceil($v);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function isNaN($f) {
		$GLOBALS['%s']->push("Math::isNaN");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_nan($f);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Math'; }
}
{
	Math::$PI = M_PI;
	Math::$NaN = acos(1.01);
	Math::$NEGATIVE_INFINITY = log(0);
	Math::$POSITIVE_INFINITY = -Math::$NEGATIVE_INFINITY;
}
