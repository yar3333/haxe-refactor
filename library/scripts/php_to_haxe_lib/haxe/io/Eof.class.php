<?php

class haxe_io_Eof {
	public function __construct(){}
	public function toString() {
		$GLOBALS['%s']->push("haxe.io.Eof::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "Eof";
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
