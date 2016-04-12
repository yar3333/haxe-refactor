<?php

class stdlib_LambdaIterable {
	public function __construct(){}
	static function findIndex($it, $f) {
		$GLOBALS['%s']->push("stdlib.LambdaIterable::findIndex");
		$__hx__spos = $GLOBALS['%s']->length;
		$n = 0;
		if(null == $it) throw new HException('null iterable');
		$__hx__it = $it->iterator();
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
		$GLOBALS['%s']->push("stdlib.LambdaIterable::sorted");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = Lambda::harray($it);
		$r->sort(stdlib_LambdaIterable_0($cmp, $it, $r));
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'stdlib.LambdaIterable'; }
}
function stdlib_LambdaIterable_0(&$cmp, &$it, &$r) {
	if($cmp !== null) {
		return $cmp;
	} else {
		return (isset(Reflect::$compare) ? Reflect::$compare: array("Reflect", "compare"));
	}
}
