<?php

class stdlib_LambdaIterator {
	public function __construct(){}
	static function harray($it) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::array");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new _hx_array(array());
		$__hx__it = $it;
		while($__hx__it->hasNext()) {
			unset($e);
			$e = $__hx__it->next();
			$r->push($e);
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function indexOf($it, $elem) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::indexOf");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = 0;
		while($it->hasNext()) {
			if((is_object($_t = $it->next()) && !($_t instanceof Enum) ? $_t === $elem : $_t == $elem)) {
				$GLOBALS['%s']->pop();
				return $r;
			}
			$r++;
			unset($_t);
		}
		{
			$GLOBALS['%s']->pop();
			return -1;
		}
		$GLOBALS['%s']->pop();
	}
	static function map($it, $conv) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::map");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new _hx_array(array());
		$__hx__it = $it;
		while($__hx__it->hasNext()) {
			unset($e);
			$e = $__hx__it->next();
			$r->push(call_user_func_array($conv, array($e)));
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function filter($it, $pred) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::filter");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new _hx_array(array());
		$__hx__it = $it;
		while($__hx__it->hasNext()) {
			unset($e);
			$e = $__hx__it->next();
			if(call_user_func_array($pred, array($e))) {
				$r->push($e);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function exists($it, $pred) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::exists");
		$__hx__spos = $GLOBALS['%s']->length;
		$__hx__it = $it;
		while($__hx__it->hasNext()) {
			unset($e);
			$e = $__hx__it->next();
			if(call_user_func_array($pred, array($e))) {
				$GLOBALS['%s']->pop();
				return true;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return false;
		}
		$GLOBALS['%s']->pop();
	}
	static function count($it, $pred = null) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::count");
		$__hx__spos = $GLOBALS['%s']->length;
		$n = 0;
		if($pred === null) {
			$__hx__it = $it;
			while($__hx__it->hasNext()) {
				unset($_);
				$_ = $__hx__it->next();
				$n++;
			}
		} else {
			$__hx__it = $it;
			while($__hx__it->hasNext()) {
				unset($x);
				$x = $__hx__it->next();
				if(call_user_func_array($pred, array($x))) {
					$n++;
				}
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $n;
		}
		$GLOBALS['%s']->pop();
	}
	static function findIndex($it, $f) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::findIndex");
		$__hx__spos = $GLOBALS['%s']->length;
		$n = 0;
		$__hx__it = $it;
		while($__hx__it->hasNext()) {
			unset($x);
			$x = $__hx__it->next();
			if(call_user_func_array($f, array($x))) {
				$GLOBALS['%s']->pop();
				return $n;
			}
			$n++;
		}
		{
			$GLOBALS['%s']->pop();
			return -1;
		}
		$GLOBALS['%s']->pop();
	}
	static function sorted($it, $cmp = null) {
		$GLOBALS['%s']->push("stdlib.LambdaIterator::sorted");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = stdlib_LambdaIterator::harray($it);
		$r->sort(stdlib_LambdaIterator_0($cmp, $it, $r));
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'stdlib.LambdaIterator'; }
}
function stdlib_LambdaIterator_0(&$cmp, &$it, &$r) {
	if($cmp !== null) {
		return $cmp;
	} else {
		return (isset(Reflect::$compare) ? Reflect::$compare: array("Reflect", "compare"));
	}
}
