<?php

class stdlib_Utf8 extends haxe_Utf8 {
	public function __construct($size = null) { if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("stdlib.Utf8::new");
		$__hx__spos = $GLOBALS['%s']->length;
		parent::__construct($size);
		$GLOBALS['%s']->pop();
	}}
	public function addString($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::addString");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = $this;
		haxe_Utf8::iter($s, array(new _hx_lambda(array(&$_g, &$s), "stdlib_Utf8_0"), 'execute'));
		$GLOBALS['%s']->pop();
	}
	static function replace($s, $from, $to) {
		$GLOBALS['%s']->push("stdlib.Utf8::replace");
		$__hx__spos = $GLOBALS['%s']->length;
		$codes = (new _hx_array(array()));
		haxe_Utf8::iter($s, array(new _hx_lambda(array(&$codes, &$from, &$s, &$to), "stdlib_Utf8_1"), 'execute'));
		$r = new stdlib_Utf8(null);
		$len = haxe_Utf8::length($from);
		if($codes->length < $len) {
			$GLOBALS['%s']->pop();
			return $s;
		}
		{
			$_g1 = 0;
			$_g = $codes->length - $len + 1;
			while($_g1 < $_g) {
				$i = $_g1++;
				$found = true;
				$j = 0;
				haxe_Utf8::iter($from, array(new _hx_lambda(array(&$_g, &$_g1, &$codes, &$found, &$from, &$i, &$j, &$len, &$r, &$s, &$to), "stdlib_Utf8_2"), 'execute'));
				if($found) {
					$r->addString($to);
				} else {
					$r->addChar($codes[$i]);
				}
				unset($j,$i,$found);
			}
		}
		{
			$_g11 = $codes->length - $len + 1;
			$_g2 = $codes->length;
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$r->addChar($codes[$i1]);
				unset($i1);
			}
		}
		{
			$tmp = $r->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function compactSpaces($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::compactSpaces");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new stdlib_Utf8(null);
		$prevSpace = false;
		haxe_Utf8::iter($s, array(new _hx_lambda(array(&$prevSpace, &$r, &$s), "stdlib_Utf8_3"), 'execute'));
		{
			$tmp = $r->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function htmlUnescape($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::htmlUnescape");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = new stdlib_Utf8(null);
		$escape = null;
		haxe_Utf8::iter($s, array(new _hx_lambda(array(&$escape, &$r, &$s), "stdlib_Utf8_4"), 'execute'));
		{
			$tmp = $r->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function htmlEscape($utf8Str, $chars = null) {
		$GLOBALS['%s']->push("stdlib.Utf8::htmlEscape");
		$__hx__spos = $GLOBALS['%s']->length;
		if($chars === null) {
			$chars = "";
		}
		$chars = "&<>" . _hx_string_or_null($chars);
		$r = new stdlib_Utf8(null);
		haxe_Utf8::iter($utf8Str, array(new _hx_lambda(array(&$chars, &$r, &$utf8Str), "stdlib_Utf8_5"), 'execute'));
		{
			$tmp = $r->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function htmlUnescapeChar($escape) {
		$GLOBALS['%s']->push("stdlib.Utf8::htmlUnescapeChar");
		$__hx__spos = $GLOBALS['%s']->length;
		if(StringTools::startsWith($escape, "#x")) {
			$tmp = stdlib_Std::parseInt("0x" . _hx_string_or_null(_hx_substr($escape, 2, null)), null);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			if(StringTools::startsWith($escape, "#")) {
				$tmp = stdlib_Std::parseInt(_hx_substr($escape, 1, null), null);
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$r = null;
				{
					$this1 = stdlib_Utf8::get_htmlUnescapeMap();
					$r = $this1->get($escape);
				}
				if($r !== null) {
					$GLOBALS['%s']->pop();
					return $r;
				}
			}
		}
		haxe_Log::trace("Unknow escape sequence: " . _hx_string_or_null($escape), _hx_anonymous(array("fileName" => "Utf8.hx", "lineNumber" => 131, "className" => "stdlib.Utf8", "methodName" => "htmlUnescapeChar")));
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	static $htmlEscapeMap;
	static function get_htmlEscapeMap() {
		$GLOBALS['%s']->push("stdlib.Utf8::get_htmlEscapeMap");
		$__hx__spos = $GLOBALS['%s']->length;
		if(stdlib_Utf8::$htmlEscapeMap === null) {
			$_g = new haxe_ds_IntMap();
			$_g->set(32, "&nbsp;");
			$_g->set(38, "&amp;");
			$_g->set(60, "&lt;");
			$_g->set(62, "&gt;");
			$_g->set(34, "&quot;");
			$_g->set(39, "&apos;");
			$_g->set(13, "&#xD;");
			$_g->set(10, "&#xA;");
			stdlib_Utf8::$htmlEscapeMap = $_g;
		}
		{
			$tmp = stdlib_Utf8::$htmlEscapeMap;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static $htmlUnescapeMap;
	static function get_htmlUnescapeMap() {
		$GLOBALS['%s']->push("stdlib.Utf8::get_htmlUnescapeMap");
		$__hx__spos = $GLOBALS['%s']->length;
		if(stdlib_Utf8::$htmlUnescapeMap === null) {
			$_g = new haxe_ds_StringMap();
			$_g->set("nbsp", 32);
			$_g->set("amp", 38);
			$_g->set("lt", 60);
			$_g->set("gt", 62);
			$_g->set("quot", 34);
			$_g->set("apos", 39);
			$_g->set("euro", 8364);
			$_g->set("iexcl", 161);
			$_g->set("cent", 162);
			$_g->set("pound", 163);
			$_g->set("curren", 164);
			$_g->set("yen", 165);
			$_g->set("brvbar", 166);
			$_g->set("sect", 167);
			$_g->set("uml", 168);
			$_g->set("copy", 169);
			$_g->set("ordf", 170);
			$_g->set("not", 172);
			$_g->set("shy", 173);
			$_g->set("reg", 174);
			$_g->set("macr", 175);
			$_g->set("deg", 176);
			$_g->set("plusmn", 177);
			$_g->set("sup2", 178);
			$_g->set("sup3", 179);
			$_g->set("acute", 180);
			$_g->set("micro", 181);
			$_g->set("para", 182);
			$_g->set("middot", 183);
			$_g->set("cedil", 184);
			$_g->set("sup1", 185);
			$_g->set("ordm", 186);
			$_g->set("raquo", 187);
			$_g->set("frac14", 188);
			$_g->set("frac12", 189);
			$_g->set("frac34", 190);
			$_g->set("iquest", 191);
			$_g->set("Agrave", 192);
			$_g->set("Aacute", 193);
			$_g->set("Acirc", 194);
			$_g->set("Atilde", 195);
			$_g->set("Auml", 196);
			$_g->set("Aring", 197);
			$_g->set("AElig", 198);
			$_g->set("Ccedil", 199);
			$_g->set("Egrave", 200);
			$_g->set("Eacute", 201);
			$_g->set("Ecirc", 202);
			$_g->set("Euml", 203);
			$_g->set("Igrave", 204);
			$_g->set("Iacute", 205);
			$_g->set("Icirc", 206);
			$_g->set("Iuml", 207);
			$_g->set("ETH", 208);
			$_g->set("Ntilde", 209);
			$_g->set("Ograve", 210);
			$_g->set("Oacute", 211);
			$_g->set("Ocirc", 212);
			$_g->set("Otilde", 213);
			$_g->set("Ouml", 214);
			$_g->set("times", 215);
			$_g->set("Oslash", 216);
			$_g->set("Ugrave", 217);
			$_g->set("Uacute", 218);
			$_g->set("Ucirc", 219);
			$_g->set("Uuml", 220);
			$_g->set("Yacute", 221);
			$_g->set("THORN", 222);
			$_g->set("szlig", 223);
			$_g->set("agrave", 224);
			$_g->set("aacute", 225);
			$_g->set("acirc", 226);
			$_g->set("atilde", 227);
			$_g->set("auml", 228);
			$_g->set("aring", 229);
			$_g->set("aelig", 230);
			$_g->set("ccedil", 231);
			$_g->set("egrave", 232);
			$_g->set("eacute", 233);
			$_g->set("ecirc", 234);
			$_g->set("euml", 235);
			$_g->set("igrave", 236);
			$_g->set("iacute", 237);
			$_g->set("icirc", 238);
			$_g->set("iuml", 239);
			$_g->set("eth", 240);
			$_g->set("ntilde", 241);
			$_g->set("ograve", 242);
			$_g->set("oacute", 243);
			$_g->set("ocirc", 244);
			$_g->set("otilde", 245);
			$_g->set("ouml", 246);
			$_g->set("divide", 247);
			$_g->set("oslash", 248);
			$_g->set("ugrave", 249);
			$_g->set("uacute", 250);
			$_g->set("ucirc", 251);
			$_g->set("uuml", 252);
			$_g->set("yacute", 253);
			$_g->set("thorn", 254);
			stdlib_Utf8::$htmlUnescapeMap = $_g;
		}
		{
			$tmp = stdlib_Utf8::$htmlUnescapeMap;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function encode($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::encode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::encode($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function decode($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::decode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::decode($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function iter($s, $chars) {
		$GLOBALS['%s']->push("stdlib.Utf8::iter");
		$__hx__spos = $GLOBALS['%s']->length;
		haxe_Utf8::iter($s, $chars);
		{
			$GLOBALS['%s']->pop();
			return;
		}
		$GLOBALS['%s']->pop();
	}
	static function charCodeAt($s, $index) {
		$GLOBALS['%s']->push("stdlib.Utf8::charCodeAt");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::charCodeAt($s, $index);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function validate($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::validate");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::validate($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function length($s) {
		$GLOBALS['%s']->push("stdlib.Utf8::length");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::length($s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function compare($a, $b) {
		$GLOBALS['%s']->push("stdlib.Utf8::compare");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::compare($a, $b);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function sub($s, $pos, $len) {
		$GLOBALS['%s']->push("stdlib.Utf8::sub");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_Utf8::sub($s, $pos, $len);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static $__properties__ = array("get_htmlUnescapeMap" => "get_htmlUnescapeMap","get_htmlEscapeMap" => "get_htmlEscapeMap");
	function __toString() { return 'stdlib.Utf8'; }
}
function stdlib_Utf8_0(&$_g, &$s, $c) {
	{
		$GLOBALS['%s']->push("stdlib.Utf8::addString@36");
		$__hx__spos2 = $GLOBALS['%s']->length;
		$_g->addChar($c);
		$GLOBALS['%s']->pop();
	}
}
function stdlib_Utf8_1(&$codes, &$from, &$s, &$to, $c) {
	{
		$GLOBALS['%s']->push("stdlib.Utf8::replace@10");
		$__hx__spos2 = $GLOBALS['%s']->length;
		$codes->push($c);
		$GLOBALS['%s']->pop();
	}
}
function stdlib_Utf8_2(&$_g, &$_g1, &$codes, &$found, &$from, &$i, &$j, &$len, &$r, &$s, &$to, $cc) {
	{
		$GLOBALS['%s']->push("stdlib.Utf8::replace@18");
		$__hx__spos2 = $GLOBALS['%s']->length;
		if($found) {
			if($codes[$i + $j] !== $cc) {
				$found = false;
			}
			$j++;
		}
		$GLOBALS['%s']->pop();
	}
}
function stdlib_Utf8_3(&$prevSpace, &$r, &$s, $c) {
	{
		$GLOBALS['%s']->push("stdlib.Utf8::compactSpaces@42");
		$__hx__spos2 = $GLOBALS['%s']->length;
		if($c === 32 || $c === 13 || $c === 10 || $c === 9) {
			if(!$prevSpace) {
				$r->addChar(32);
				$prevSpace = true;
			}
		} else {
			$r->addChar($c);
			$prevSpace = false;
		}
		$GLOBALS['%s']->pop();
	}
}
function stdlib_Utf8_4(&$escape, &$r, &$s, $c) {
	{
		$GLOBALS['%s']->push("stdlib.Utf8::htmlUnescape@69");
		$__hx__spos2 = $GLOBALS['%s']->length;
		if($escape !== null) {
			if($c === 59) {
				$chr = stdlib_Utf8::htmlUnescapeChar($escape);
				if($chr !== null) {
					$r->addChar($chr);
				}
				$escape = null;
			} else {
				$escape .= _hx_string_or_null(chr($c));
			}
		} else {
			if($c === 38) {
				$escape = "";
			} else {
				$r->addChar($c);
			}
		}
		$GLOBALS['%s']->pop();
	}
}
function stdlib_Utf8_5(&$chars, &$r, &$utf8Str, $c) {
	{
		$GLOBALS['%s']->push("stdlib.Utf8::htmlEscape@104");
		$__hx__spos2 = $GLOBALS['%s']->length;
		$s = null;
		{
			$this1 = stdlib_Utf8::get_htmlEscapeMap();
			$s = $this1->get($c);
		}
		if($s !== null && $c >= 0 && $c <= 255 && _hx_index_of($chars, chr($c), null) >= 0) {
			$r->addString($s);
		} else {
			$r->addChar($c);
		}
		$GLOBALS['%s']->pop();
	}
}
