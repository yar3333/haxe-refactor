<?php

class stdlib_Exception {
	public function __construct($message = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("stdlib.Exception::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($message === null) {
			$this->message = "";
		} else {
			$this->message = $message;
		}
		$this->stack = haxe_CallStack::callStack();
		$this->stack->shift();
		$GLOBALS['%s']->pop();
	}}
	public $message;
	public $stack;
	public function toString() {
		$GLOBALS['%s']->push("stdlib.Exception::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_string_or_null($this->message) . "\x0AStack trace:\x0A\x09" . _hx_string_or_null(stdlib_Exception_0($this));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
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
	static function string($e) {
		$GLOBALS['%s']->push("stdlib.Exception::string");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = Std::string($e);
		if(!Std::is($e, _hx_qtype("stdlib.Exception"))) {
			$stack = haxe_CallStack::toString(haxe_CallStack::exceptionStack());
			if($stack !== "") {
				$r .= "\x0AStack trace:\x0A\x09" . _hx_string_or_null(stdlib_Exception_1($e, $r, $stack));
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function rethrow($exception) {
		$GLOBALS['%s']->push("stdlib.Exception::rethrow");
		$__hx__spos = $GLOBALS['%s']->length;
		throw new HException(stdlib_Exception::wrap($exception));
		$GLOBALS['%s']->pop();
	}
	static function wrap($e) {
		$GLOBALS['%s']->push("stdlib.Exception::wrap");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!Std::is($e, _hx_qtype("stdlib.Exception"))) {
			$r = new stdlib_Exception(Std::string($e));
			$r->stack = haxe_CallStack::exceptionStack();
			{
				$GLOBALS['%s']->pop();
				return $r;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $e;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
function stdlib_Exception_0(&$__hx__this) {
	{
		$s = null;
		{
			$s1 = haxe_CallStack::toString($__hx__this->stack);
			$s = ltrim($s1);
		}
		return str_replace("\x0A", "\x0A\x09", $s);
	}
}
function stdlib_Exception_1(&$e, &$r, &$stack) {
	{
		$s = ltrim($stack);
		return str_replace("\x0A", "\x0A\x09", $s);
	}
}
