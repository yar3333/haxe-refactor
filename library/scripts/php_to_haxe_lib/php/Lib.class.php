<?php

class php_Lib {
	public function __construct(){}
	static function toPhpArray($a) {
		$GLOBALS['%s']->push("php.Lib::toPhpArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $a->a;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hashOfAssociativeArray($arr) {
		$GLOBALS['%s']->push("php.Lib::hashOfAssociativeArray");
		$__hx__spos = $GLOBALS['%s']->length;
		$h = new haxe_ds_StringMap();
		$h->h = $arr;
		{
			$tmp = $h;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function associativeArrayOfHash($hash) {
		$GLOBALS['%s']->push("php.Lib::associativeArrayOfHash");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $hash->h;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'php.Lib'; }
}
