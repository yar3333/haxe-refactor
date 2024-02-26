<?php
/**
 * Generated by Haxe 4.0.0-rc.2+77068e10c
 */

namespace stdlib;

use \php\Boot;
use \haxe\Utf8 as HaxeUtf8;

class StringTools {
	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function addcslashes ($s) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:67: characters 9-66
		return addcslashes($s, "'\"\x09\x0D\x0A\\");
	}

	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function capitalize ($s) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:193: characters 62-118
		if ($s === "") {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:193: characters 72-73
			return $s;
		} else {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:193: characters 76-118
			return (mb_strtoupper(mb_substr($s, 0, 1))??'null') . (mb_substr($s, 1, null)??'null');
		}
	}

	/**
	 * @param string $s
	 * @param string $end
	 * 
	 * @return bool
	 */
	static public function endsWith ($s, $end) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:47: lines 47-49
		return \StringTools::endsWith($s, $end);
	}

	/**
	 * @param string $s
	 * @param int $index
	 * 
	 * @return int
	 */
	static public function fastCodeAt ($s, $index) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:113: lines 113-117
		return \StringTools::fastCodeAt($s, $index);
	}

	/**
	 * @param string $template
	 * @param mixed $value
	 * 
	 * @return string
	 */
	static public function format ($template, $value) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:127: characters 3-54
		return sprintf($template, $value);
	}

	/**
	 * @param int $n
	 * @param int $digits
	 * 
	 * @return string
	 */
	static public function hex ($n, $digits = null) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:103: lines 103-111
		return \StringTools::hex($n, $digits);
	}

	/**
	 * @param string $s
	 * 
	 * @return int
	 */
	static public function hexdec ($s) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:58: characters 3-39
		return hexdec($s);
	}

	/**
	 * @param string $s
	 * @param bool $quotes
	 * 
	 * @return string
	 */
	static public function htmlEscape ($s, $quotes = null) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:35: lines 35-37
		return htmlspecialchars($s, ($quotes ? ENT_QUOTES | ENT_HTML401 : ENT_NOQUOTES));
	}

	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function htmlUnescape ($s) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:39: lines 39-41
		return htmlspecialchars_decode($s, ENT_QUOTES);
	}

	/**
	 * @param int $c
	 * 
	 * @return bool
	 */
	static public function isEof ($c) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:119: lines 119-121
		return $c === 0;
	}

	/**
	 * @param string $s
	 * 
	 * @return bool
	 */
	static public function isNullOrEmpty ($s) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:191: characters 63-83
		if ($s !== null) {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:191: characters 76-83
			return $s === "";
		} else {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:191: characters 63-83
			return true;
		}
	}

	/**
	 * @param string $s
	 * @param int $pos
	 * 
	 * @return bool
	 */
	static public function isSpace ($s, $pos) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:51: lines 51-54
		return \StringTools::isSpace($s, $pos);
	}

	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function jsonEscape ($s) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:138: characters 9-37
		if ($s === null) {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:138: characters 24-37
			return "null";
		}
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:140: characters 9-58
		$r = new Utf8(mb_strlen($s) + (int)((mb_strlen($s) / 5)));
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:142: characters 9-28
		$r->addChar(34);
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:144: lines 144-184
		HaxeUtf8::iter($s, function ($c)  use (&$r) {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:146: lines 146-183
			if ($c === 9) {
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:157: characters 6-26
				$r->addChar(92);
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:158: characters 6-25
				$r->addChar(116);
			} else if ($c === 10) {
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:161: characters 6-26
				$r->addChar(92);
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:162: characters 6-25
				$r->addChar(110);
			} else if ($c === 13) {
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:165: characters 6-26
				$r->addChar(92);
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:166: characters 6-25
				$r->addChar(114);
			} else if ($c === 34) {
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:153: characters 6-26
				$r->addChar(92);
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:154: characters 6-25
				$r->addChar(34);
			} else if ($c === 92) {
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:149: characters 6-26
				$r->addChar(92);
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:150: characters 6-26
				$r->addChar(92);
			} else {
				#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:169: lines 169-182
				if ($c < 32) {
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:171: characters 7-27
					$r->addChar(92);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:172: characters 7-26
					$r->addChar(117);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:173: characters 7-37
					$t = \StringTools::hex($c, 4);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:174: characters 17-45
					$tmp = \StringTools::fastCodeAt($t, 0);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:174: characters 7-46
					$r->addChar($tmp);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:175: characters 17-45
					$tmp1 = \StringTools::fastCodeAt($t, 1);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:175: characters 7-46
					$r->addChar($tmp1);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:176: characters 17-45
					$tmp2 = \StringTools::fastCodeAt($t, 2);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:176: characters 7-46
					$r->addChar($tmp2);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:177: characters 17-45
					$tmp3 = \StringTools::fastCodeAt($t, 3);
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:177: characters 7-46
					$r->addChar($tmp3);
				} else {
					#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:181: characters 7-19
					$r->addChar($c);
				}
			}
		});
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:186: characters 3-22
		$r->addChar(34);
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:188: characters 3-22
		return $r->toString();
	}

	/**
	 * @param string $s
	 * @param string $c
	 * @param int $l
	 * 
	 * @return string
	 */
	static public function lpad ($s, $c, $l) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:82: lines 82-94
		return \StringTools::lpad($s, $c, $l);
	}

	/**
	 * @param string $s
	 * @param string $chars
	 * 
	 * @return string
	 */
	static public function ltrim ($s, $chars = null) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:11: characters 10-92
		if ($chars === null) {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:11: characters 34-54
			return ltrim($s);
		} else {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:11: characters 65-92
			return ltrim($s, $chars);
		}
	}

	/**
	 * Returns a String that can be used as a single command line argument
	 * on Unix.
	 * The input will be quoted, or escaped if necessary.
	 * 
	 * @param string $argument
	 * 
	 * @return string
	 */
	static public function quoteUnixArg ($argument) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:128: lines 128-141
		return \StringTools::quoteUnixArg($argument);
	}

	/**
	 * Returns a String that can be used as a single command line argument
	 * on Windows.
	 * The input will be quoted, or escaped if necessary, such that the output
	 * will be parsed as a single argument using the rule specified in
	 * http://msdn.microsoft.com/en-us/library/ms880421
	 * Examples:
	 * ```haxe
	 * quoteWinArg("abc") == "abc";
	 * quoteWinArg("ab c") == '"ab c"';
	 * ```
	 * 
	 * @param string $argument
	 * @param bool $escapeMetaCharacters
	 * 
	 * @return string
	 */
	static public function quoteWinArg ($argument, $escapeMetaCharacters) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:161: lines 161-221
		return \StringTools::quoteWinArg($argument, $escapeMetaCharacters);
	}

	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function regexEscape ($s) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:133: characters 3-69
		return (new \EReg("([\\-\\[\\]/\\{\\}\\(\\)\\*\\+\\?\\.\\\\\\^\\\$\\|])", "g"))->replace($s, "\\\$1");
	}

	/**
	 * @param string $s
	 * @param string $sub
	 * @param string $by
	 * 
	 * @return string
	 */
	static public function replace ($s, $sub, $by) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:96: lines 96-101
		return \StringTools::replace($s, $sub, $by);
	}

	/**
	 * @param string $s
	 * @param string $c
	 * @param int $l
	 * 
	 * @return string
	 */
	static public function rpad ($s, $c, $l) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:68: lines 68-80
		return \StringTools::rpad($s, $c, $l);
	}

	/**
	 * @param string $s
	 * @param string $chars
	 * 
	 * @return string
	 */
	static public function rtrim ($s, $chars = null) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:28: characters 10-92
		if ($chars === null) {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:28: characters 34-54
			return rtrim($s);
		} else {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:28: characters 65-92
			return rtrim($s, $chars);
		}
	}

	/**
	 * @param string $s
	 * @param string $start
	 * 
	 * @return bool
	 */
	static public function startsWith ($s, $start) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:43: lines 43-45
		return \StringTools::startsWith($s, $start);
	}

	/**
	 * allowedTags example: "<a><p>".
	 * 
	 * @param string $str
	 * @param string $allowedTags
	 * 
	 * @return string
	 */
	static public function stripTags ($str, $allowedTags = "") {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:80: characters 3-58
		if ($allowedTags === null) {
			$allowedTags = "";
		}
		return strip_tags($str, $allowedTags);
	}

	/**
	 * @param string $s
	 * @param string $chars
	 * 
	 * @return string
	 */
	static public function trim ($s, $chars = null) {
		#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:45: characters 10-90
		if ($chars === null) {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:45: characters 34-53
			return trim($s);
		} else {
			#c:\MyProg\_haxelibs\stdlib\library\stdlib/StringTools.hx:45: characters 64-90
			return trim($s, $chars);
		}
	}

	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function urlDecode ($s) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:31: lines 31-33
		return urldecode($s);
	}

	/**
	 * @param string $s
	 * 
	 * @return string
	 */
	static public function urlEncode ($s) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/StringTools.hx:27: lines 27-29
		return rawurlencode($s);
	}
}

Boot::registerClass(StringTools::class, 'stdlib.StringTools');