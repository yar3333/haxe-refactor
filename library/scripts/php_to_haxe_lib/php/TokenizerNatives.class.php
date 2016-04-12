<?php

class php_TokenizerNatives {
	public function __construct(){}
	static function token_get_all($source) {
		$GLOBALS['%s']->push("php.TokenizerNatives::token_get_all");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = token_get_all($source);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function token_name($token) {
		$GLOBALS['%s']->push("php.TokenizerNatives::token_name");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = token_name($token);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'php.TokenizerNatives'; }
}
