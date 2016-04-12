<?php

class php__TypedArray_TypedArray_Impl_ {
	public function __construct(){}
	static function _new() {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::_new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this1 = null;
		$this1 = array();
		{
			$tmp = $this1;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function iterator($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::iterator");
		$__hx__spos = $GLOBALS['%s']->length;
		$a = null;
		{
			$a1 = array_values($this1);
			$a = new _hx_array($a1);
		}
		{
			$tmp = $a->iterator();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function foreachKeyValue($this1, $callb) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::foreachKeyValue");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = 0;
		$_g1 = null;
		{
			$a = array_keys($this1);
			$_g1 = new _hx_array($a);
		}
		while($_g < $_g1->length) {
			$k = $_g1[$_g];
			++$_g;
			call_user_func_array($callb, array($k, $this1[$k]));
			unset($k);
		}
		$GLOBALS['%s']->pop();
	}
	static function mapKeyValue($this1, $callb) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::mapKeyValue");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = null;
			{
				$a = array_keys($this1);
				$_g1 = new _hx_array($a);
			}
			while($_g < $_g1->length) {
				$k = $_g1[$_g];
				++$_g;
				$r->push(call_user_func_array($callb, array($k, $this1[$k])));
				unset($k);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	static function get_length($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::get_length");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = count($this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function fromMap($m) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::fromMap");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = php_Lib::associativeArrayOfHash($m);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function fromArray($a) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::fromArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = php_Lib::toPhpArray($a);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function toArray($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::toArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new _hx_array($this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function toMap($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::toMap");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = php_Lib::hashOfAssociativeArray($this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function get($this1, $k) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::get");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this1[$k];
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function set($this1, $k, $v) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::set");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this1[$k] = $v;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function push($this1, $v) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::push");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = array_push($v);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function pop($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::pop");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = array_pop();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function unshift($this1, $v) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::unshift");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = array_unshift($v);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function shift($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::shift");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = array_shift();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function join($this1, $glue = null) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::join");
		$__hx__spos = $GLOBALS['%s']->length;
		if($glue === null) {
			$glue = "";
		}
		{
			$tmp = implode($glue, $this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function splice($this1, $offset, $length = null, $replacement = null) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::splice");
		$__hx__spos = $GLOBALS['%s']->length;
		if($length === null) {
			$length = count($this1) - $offset;
		}
		if($replacement === null) {
			$replacement = array();
		}
		{
			$tmp = array_splice($this1, $offset, $length, $replacement);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function asort($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::asort");
		$__hx__spos = $GLOBALS['%s']->length;
		asort($this1);
		$GLOBALS['%s']->pop();
	}
	static function hasKey($this1, $k) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::hasKey");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = array_key_exists($k, $this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hasValue($this1, $v, $strict = null) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::hasValue");
		$__hx__spos = $GLOBALS['%s']->length;
		if($strict === null) {
			$strict = false;
		}
		{
			$tmp = in_array($v, $this1, $strict);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function removeKey($this1, $k) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::removeKey");
		$__hx__spos = $GLOBALS['%s']->length;
		unset($this1[$k]);
		$GLOBALS['%s']->pop();
	}
	static function keys($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::keys");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = php__TypedArray_TypedArray_Impl__0($this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function values($this1) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::values");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = php__TypedArray_TypedArray_Impl__1($this1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function plus($this1, $arr) {
		$GLOBALS['%s']->push("php._TypedArray.TypedArray_Impl_::plus");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this1 + $arr;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static $__properties__ = array("get_length" => "get_length");
	function __toString() { return 'php._TypedArray.TypedArray_Impl_'; }
}
function php__TypedArray_TypedArray_Impl__0(&$this1) {
	{
		$a = array_keys($this1);
		return new _hx_array($a);
	}
}
function php__TypedArray_TypedArray_Impl__1(&$this1) {
	{
		$a = array_values($this1);
		return new _hx_array($a);
	}
}
