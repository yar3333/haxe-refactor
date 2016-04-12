<?php

class StringTools {
	public function __construct(){}
	static function htmlEscape($s, $quotes = null) {
		$GLOBALS['%s']->push("StringTools::htmlEscape");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = _hx_explode(">", _hx_explode("<", _hx_explode("&", $s)->join("&amp;"))->join("&lt;"))->join("&gt;");
		if($quotes) {
			$tmp = _hx_explode("'", _hx_explode("\"", $s)->join("&quot;"))->join("&#039;");
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$GLOBALS['%s']->pop();
			return $s;
		}
		$GLOBALS['%s']->pop();
	}
	static function startsWith($s, $start) {
		$GLOBALS['%s']->push("StringTools::startsWith");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = strlen($s) >= strlen($start) && _hx_substr($s, 0, strlen($start)) === $start;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function endsWith($s, $end) {
		$GLOBALS['%s']->push("StringTools::endsWith");
		$__hx__spos = $GLOBALS['%s']->length;
		$elen = strlen($end);
		$slen = strlen($s);
		{
			$tmp = $slen >= $elen && _hx_substr($s, $slen - $elen, $elen) === $end;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function isSpace($s, $pos) {
		$GLOBALS['%s']->push("StringTools::isSpace");
		$__hx__spos = $GLOBALS['%s']->length;
		$c = _hx_char_code_at($s, $pos);
		{
			$tmp = $c >= 9 && $c <= 13 || $c === 32;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hex($n, $digits = null) {
		$GLOBALS['%s']->push("StringTools::hex");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = dechex($n);
		$len = 8;
		if(strlen($s) > (StringTools_0($digits, $len, $n, $s))) {
			$s = _hx_substr($s, -$len, null);
		} else {
			if($digits !== null) {
				if(strlen("0") === 0 || strlen($s) >= $digits) {
					$s = $s;
				} else {
					$s = str_pad($s, Math::ceil(($digits - strlen($s)) / strlen("0")) * strlen("0") + strlen($s), "0", STR_PAD_LEFT);
				}
			}
		}
		{
			$tmp = strtoupper($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'StringTools'; }
}
function StringTools_0(&$digits, &$len, &$n, &$s) {
	if(null === $digits) {
		return $len;
	} else {
		if($digits > $len) {
			return $len = $digits;
		} else {
			return $len = $len;
		}
	}
}
