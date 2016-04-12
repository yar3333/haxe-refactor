<?php

class haxe_Utf8 {
	public function __construct($size = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("haxe.Utf8::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->__b = "";
		$GLOBALS['%s']->pop();
	}}
	public $__b;
	public function addChar($c) {
		$GLOBALS['%s']->push("haxe.Utf8::addChar");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->__b .= _hx_string_or_null(haxe_Utf8::uchr($c));
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("haxe.Utf8::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->__b;
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
	static function encode($s) {
		$GLOBALS['%s']->push("haxe.Utf8::encode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = utf8_encode($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function decode($s) {
		$GLOBALS['%s']->push("haxe.Utf8::decode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = utf8_decode($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function iter($s, $chars) {
		$GLOBALS['%s']->push("haxe.Utf8::iter");
		$__hx__spos = $GLOBALS['%s']->length;
		$len = haxe_Utf8::length($s);
		{
			$_g = 0;
			while($_g < $len) {
				$i = $_g++;
				call_user_func_array($chars, array(haxe_Utf8::charCodeAt($s, $i)));
				unset($i);
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function charCodeAt($s, $index) {
		$GLOBALS['%s']->push("haxe.Utf8::charCodeAt");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::uord(haxe_Utf8::sub($s, $index, 1));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function uchr($i) {
		$GLOBALS['%s']->push("haxe.Utf8::uchr");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = mb_convert_encoding(pack('N',$i), 'UTF-8', 'UCS-4BE');
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function uord($s) {
		$GLOBALS['%s']->push("haxe.Utf8::uord");
		$__hx__spos = $GLOBALS['%s']->length;
		$c = unpack('N', mb_convert_encoding($s, 'UCS-4BE', 'UTF-8'));
		{
			$tmp = $c[1];
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function validate($s) {
		$GLOBALS['%s']->push("haxe.Utf8::validate");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = mb_check_encoding($s, "UTF-8");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function length($s) {
		$GLOBALS['%s']->push("haxe.Utf8::length");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = mb_strlen($s, "UTF-8");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function compare($a, $b) {
		$GLOBALS['%s']->push("haxe.Utf8::compare");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = strcmp($a, $b);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function sub($s, $pos, $len) {
		$GLOBALS['%s']->push("haxe.Utf8::sub");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = mb_substr($s, $pos, $len, "UTF-8");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
