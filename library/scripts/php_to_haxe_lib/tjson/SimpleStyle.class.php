<?php

class tjson_SimpleStyle implements tjson_EncodeStyle{
	public function __construct() { if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public function beginObject($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::beginObject");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "{";
		}
		$GLOBALS['%s']->pop();
	}
	public function endObject($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::endObject");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "}";
		}
		$GLOBALS['%s']->pop();
	}
	public function beginArray($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::beginArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "[";
		}
		$GLOBALS['%s']->pop();
	}
	public function endArray($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::endArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "]";
		}
		$GLOBALS['%s']->pop();
	}
	public function firstEntry($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::firstEntry");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "";
		}
		$GLOBALS['%s']->pop();
	}
	public function entrySeperator($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::entrySeperator");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return ",";
		}
		$GLOBALS['%s']->pop();
	}
	public function keyValueSeperator($depth) {
		$GLOBALS['%s']->push("tjson.SimpleStyle::keyValueSeperator");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return ":";
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'tjson.SimpleStyle'; }
}
