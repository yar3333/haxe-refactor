<?php

class stdlib_StringTools {
	public function __construct(){}
	static function ltrim($s, $chars = null) {
		$GLOBALS['%s']->push("stdlib.StringTools::ltrim");
		$__hx__spos = $GLOBALS['%s']->length;
		if($chars === null) {
			$tmp = ltrim($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = ltrim($s, $chars);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function rtrim($s, $chars = null) {
		$GLOBALS['%s']->push("stdlib.StringTools::rtrim");
		$__hx__spos = $GLOBALS['%s']->length;
		if($chars === null) {
			$tmp = rtrim($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = rtrim($s, $chars);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function trim($s, $chars = null) {
		$GLOBALS['%s']->push("stdlib.StringTools::trim");
		$__hx__spos = $GLOBALS['%s']->length;
		if($chars === null) {
			$tmp = trim($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = trim($s, $chars);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hexdec($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::hexdec");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = hexdec($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function addcslashes($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::addcslashes");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = addcslashes($s, "'\"\x09\x0D\x0A\\");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function stripTags($str, $allowedTags = null) {
		$GLOBALS['%s']->push("stdlib.StringTools::stripTags");
		$__hx__spos = $GLOBALS['%s']->length;
		if($allowedTags === null) {
			$allowedTags = "";
		}
		{
			$tmp = strip_tags($str, $allowedTags);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function format($template, $value) {
		$GLOBALS['%s']->push("stdlib.StringTools::format");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = sprintf($template, $value);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function regexEscape($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::regexEscape");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_deref(new EReg("([\\-\\[\\]/\\{\\}\\(\\)\\*\\+\\?\\.\\\\\\^\\\$\\|])", "g"))->replace($s, "\\\$1");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function jsonEscape($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::jsonEscape");
		$__hx__spos = $GLOBALS['%s']->length;
		if($s === null) {
			$GLOBALS['%s']->pop();
			return "null";
		}
		$r = new stdlib_Utf8(strlen($s) + Std::int(strlen($s) / 5));
		$r->addChar(34);
		haxe_Utf8::iter($s, array(new _hx_lambda(array(&$r, &$s), "stdlib_StringTools_0"), 'execute'));
		$r->addChar(34);
		{
			$tmp = $r->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function isEmpty($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::isEmpty");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $s === null || $s === "";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function capitalize($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::capitalize");
		$__hx__spos = $GLOBALS['%s']->length;
		if(stdlib_StringTools::isEmpty($s)) {
			$GLOBALS['%s']->pop();
			return $s;
		} else {
			$tmp = _hx_string_or_null(strtoupper(_hx_substr($s, 0, 1))) . _hx_string_or_null(_hx_substr($s, 1, null));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function urlEncode($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::urlEncode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = rawurlencode($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function urlDecode($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::urlDecode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = urldecode($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function htmlEscape($s, $quotes = null) {
		$GLOBALS['%s']->push("stdlib.StringTools::htmlEscape");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = StringTools::htmlEscape($s, $quotes);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function htmlUnescape($s) {
		$GLOBALS['%s']->push("stdlib.StringTools::htmlUnescape");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = htmlspecialchars_decode($s, ENT_QUOTES);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function startsWith($s, $start) {
		$GLOBALS['%s']->push("stdlib.StringTools::startsWith");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = StringTools::startsWith($s, $start);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function endsWith($s, $end) {
		$GLOBALS['%s']->push("stdlib.StringTools::endsWith");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = StringTools::endsWith($s, $end);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function isSpace($s, $pos) {
		$GLOBALS['%s']->push("stdlib.StringTools::isSpace");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = StringTools::isSpace($s, $pos);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function rpad($s, $c, $l) {
		$GLOBALS['%s']->push("stdlib.StringTools::rpad");
		$__hx__spos = $GLOBALS['%s']->length;
		if(strlen($c) === 0 || strlen($s) >= $l) {
			$GLOBALS['%s']->pop();
			return $s;
		} else {
			$tmp = str_pad($s, Math::ceil(($l - strlen($s)) / strlen($c)) * strlen($c) + strlen($s), $c, STR_PAD_RIGHT);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function lpad($s, $c, $l) {
		$GLOBALS['%s']->push("stdlib.StringTools::lpad");
		$__hx__spos = $GLOBALS['%s']->length;
		if(strlen($c) === 0 || strlen($s) >= $l) {
			$GLOBALS['%s']->pop();
			return $s;
		} else {
			$tmp = str_pad($s, Math::ceil(($l - strlen($s)) / strlen($c)) * strlen($c) + strlen($s), $c, STR_PAD_LEFT);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function replace($s, $sub, $by) {
		$GLOBALS['%s']->push("stdlib.StringTools::replace");
		$__hx__spos = $GLOBALS['%s']->length;
		if($sub === "") {
			$tmp = implode(str_split ($s), $by);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = str_replace($sub, $by, $s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hex($n, $digits = null) {
		$GLOBALS['%s']->push("stdlib.StringTools::hex");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = StringTools::hex($n, $digits);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function fastCodeAt($s, $index) {
		$GLOBALS['%s']->push("stdlib.StringTools::fastCodeAt");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = ord(substr($s,$index,1));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function isEof($c) {
		$GLOBALS['%s']->push("stdlib.StringTools::isEof");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = ($c === 0);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'stdlib.StringTools'; }
}
function stdlib_StringTools_0(&$r, &$s, $c) {
	{
		$GLOBALS['%s']->push("stdlib.StringTools::jsonEscape@144");
		$__hx__spos2 = $GLOBALS['%s']->length;
		switch($c) {
		case 92:{
			$r->addChar(92);
			$r->addChar(92);
		}break;
		case 34:{
			$r->addChar(92);
			$r->addChar(34);
		}break;
		case 9:{
			$r->addChar(92);
			$r->addChar(116);
		}break;
		case 10:{
			$r->addChar(92);
			$r->addChar(110);
		}break;
		case 13:{
			$r->addChar(92);
			$r->addChar(114);
		}break;
		default:{
			if($c < 32) {
				$r->addChar(92);
				$r->addChar(117);
				$t = StringTools::hex($c, 4);
				$r->addChar(ord(substr($t,0,1)));
				$r->addChar(ord(substr($t,1,1)));
				$r->addChar(ord(substr($t,2,1)));
				$r->addChar(ord(substr($t,3,1)));
			} else {
				$r->addChar($c);
			}
		}break;
		}
		$GLOBALS['%s']->pop();
	}
}
