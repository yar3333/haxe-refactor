<?php

class Sys {
	public function __construct(){}
	static function hprint($v) {
		$GLOBALS['%s']->push("Sys::print");
		$__hx__spos = $GLOBALS['%s']->length;
		echo(Std::string($v));
		$GLOBALS['%s']->pop();
	}
	static function println($v) {
		$GLOBALS['%s']->push("Sys::println");
		$__hx__spos = $GLOBALS['%s']->length;
		Sys::hprint($v);
		Sys::hprint("\x0A");
		$GLOBALS['%s']->pop();
	}
	static function args() {
		$GLOBALS['%s']->push("Sys::args");
		$__hx__spos = $GLOBALS['%s']->length;
		if(array_key_exists("argv", $_SERVER)) {
			$tmp = new _hx_array(array_slice($_SERVER["argv"], 1));
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = (new _hx_array(array()));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function executablePath() {
		$GLOBALS['%s']->push("Sys::executablePath");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $_SERVER['SCRIPT_FILENAME'];
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Sys'; }
}
