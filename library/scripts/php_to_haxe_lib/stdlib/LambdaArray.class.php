<?php

class stdlib_LambdaArray {
	public function __construct(){}
	static function insertRange($arr, $pos, $range) {
		$GLOBALS['%s']->push("stdlib.LambdaArray::insertRange");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = 0;
		while($_g < $range->length) {
			$e = $range[$_g];
			++$_g;
			$arr->insert($pos++, $e);
			unset($e);
		}
		$GLOBALS['%s']->pop();
	}
	static function extract($arr, $f) {
		$GLOBALS['%s']->push("stdlib.LambdaArray::extract");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = (new _hx_array(array()));
		$i = 0;
		while($i < $arr->length) {
			if(call_user_func_array($f, array($arr[$i]))) {
				$r->push($arr[$i]);
				$arr->splice($i, 1);
			} else {
				$i++;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function spliceEx($arr, $pos, $len = null, $replacement = null) {
		$GLOBALS['%s']->push("stdlib.LambdaArray::spliceEx");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = $arr->splice($pos, stdlib_LambdaArray_0($arr, $len, $pos, $replacement));
		if($replacement !== null) {
			stdlib_LambdaArray::insertRange($arr, $pos, $replacement);
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'stdlib.LambdaArray'; }
}
function stdlib_LambdaArray_0(&$arr, &$len, &$pos, &$replacement) {
	if($len !== null) {
		return $len;
	} else {
		return $arr->length - $pos;
	}
}
