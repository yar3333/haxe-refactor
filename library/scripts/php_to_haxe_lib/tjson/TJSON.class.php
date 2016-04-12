<?php

class tjson_TJSON {
	public function __construct(){}
	static $OBJECT_REFERENCE_PREFIX = "@~obRef#";
	static function parse($json, $fileName = null, $stringProcessor = null) {
		$GLOBALS['%s']->push("tjson.TJSON::parse");
		$__hx__spos = $GLOBALS['%s']->length;
		if($fileName === null) {
			$fileName = "JSON Data";
		}
		$t = new tjson_TJSONParser($json, $fileName, $stringProcessor);
		{
			$tmp = $t->doParse();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function encode($obj, $style = null, $useCache = null) {
		$GLOBALS['%s']->push("tjson.TJSON::encode");
		$__hx__spos = $GLOBALS['%s']->length;
		if($useCache === null) {
			$useCache = true;
		}
		$t = new tjson_TJSONEncoder($useCache);
		{
			$tmp = $t->doEncode($obj, $style);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'tjson.TJSON'; }
}
