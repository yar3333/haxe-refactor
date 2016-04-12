<?php

class StringBuf {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("StringBuf::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->b = "";
		$GLOBALS['%s']->pop();
	}}
	public $b;
	public function add($x) {
		$GLOBALS['%s']->push("StringBuf::add");
		$__hx__spos = $GLOBALS['%s']->length;
		if(is_null($x)) {
			$x = "null";
		} else {
			if(is_bool($x)) {
				$x = (($x) ? "true" : "false");
			}
		}
		$this->b .= Std::string($x);
		$GLOBALS['%s']->pop();
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'StringBuf'; }
}
