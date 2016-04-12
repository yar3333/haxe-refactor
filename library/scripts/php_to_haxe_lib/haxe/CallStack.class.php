<?php

class haxe_CallStack {
	public function __construct(){}
	static function callStack() {
		$GLOBALS['%s']->push("haxe.CallStack::callStack");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_CallStack::makeStack("%s");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function exceptionStack() {
		$GLOBALS['%s']->push("haxe.CallStack::exceptionStack");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_CallStack::makeStack("%e");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function toString($stack) {
		$GLOBALS['%s']->push("haxe.CallStack::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		$b = new StringBuf();
		{
			$_g = 0;
			while($_g < $stack->length) {
				$s = $stack[$_g];
				++$_g;
				$b->add("\x0ACalled from ");
				haxe_CallStack::itemToString($b, $s);
				unset($s);
			}
		}
		{
			$tmp = $b->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function itemToString($b, $s) {
		$GLOBALS['%s']->push("haxe.CallStack::itemToString");
		$__hx__spos = $GLOBALS['%s']->length;
		switch($s->index) {
		case 0:{
			$b->add("a C function");
		}break;
		case 1:{
			$m = _hx_deref($s)->params[0];
			{
				$b->add("module ");
				$b->add($m);
			}
		}break;
		case 2:{
			$line = _hx_deref($s)->params[2];
			$file = _hx_deref($s)->params[1];
			$s1 = _hx_deref($s)->params[0];
			{
				if($s1 !== null) {
					haxe_CallStack::itemToString($b, $s1);
					$b->add(" (");
				}
				$b->add($file);
				$b->add(" line ");
				$b->add($line);
				if($s1 !== null) {
					$b->add(")");
				}
			}
		}break;
		case 3:{
			$meth = _hx_deref($s)->params[1];
			$cname = _hx_deref($s)->params[0];
			{
				$b->add($cname);
				$b->add(".");
				$b->add($meth);
			}
		}break;
		case 4:{
			$n = _hx_deref($s)->params[0];
			{
				$b->add("local function #");
				$b->add($n);
			}
		}break;
		}
		$GLOBALS['%s']->pop();
	}
	static function makeStack($s) {
		$GLOBALS['%s']->push("haxe.CallStack::makeStack");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!isset($GLOBALS[$s])) {
			$tmp = (new _hx_array(array()));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$a = $GLOBALS[$s];
		$m = (new _hx_array(array()));
		{
			$_g1 = 0;
			$_g = null;
			$_g = $a->length - ((($s === "%s") ? 2 : 0));
			while($_g1 < $_g) {
				$i = $_g1++;
				$d = _hx_explode("::", $a[$i]);
				$m->unshift(haxe_StackItem::Method($d[0], $d[1]));
				unset($i,$d);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $m;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'haxe.CallStack'; }
}
