<?php

class php_PcreNatives {
	public function __construct(){}
	static function get_PREG_PATTERN_ORDER() {
		$GLOBALS['%s']->push("php.PcreNatives::get_PREG_PATTERN_ORDER");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = PREG_PATTERN_ORDER;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function get_PREG_SET_ORDER() {
		$GLOBALS['%s']->push("php.PcreNatives::get_PREG_SET_ORDER");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = PREG_SET_ORDER;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function get_PREG_OFFSET_CAPTURE() {
		$GLOBALS['%s']->push("php.PcreNatives::get_PREG_OFFSET_CAPTURE");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = PREG_OFFSET_CAPTURE;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_filter($pattern, $replacement, $subject, $limit = null, $count = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_filter");
		$__hx__spos = $GLOBALS['%s']->length;
		if($limit === null) {
			$limit = -1;
		}
		if($count === null) {
			$tmp = preg_filter($pattern, $replacement, $subject, $limit);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = preg_filter($pattern, $replacement, $subject, $limit, $count);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_grep($pattern, $input, $flags = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_grep");
		$__hx__spos = $GLOBALS['%s']->length;
		if($flags === null) {
			$flags = 0;
		}
		{
			$tmp = preg_grep($pattern, $input, $flags);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_last_error() {
		$GLOBALS['%s']->push("php.PcreNatives::preg_last_error");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = preg_last_error();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_match_all($pattern, $subject) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_match_all");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = preg_match_all($pattern, $subject);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_match_all_ex($pattern, $subject, $matches, $flags = null, $offset = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_match_all_ex");
		$__hx__spos = $GLOBALS['%s']->length;
		if($offset === null) {
			$offset = 0;
		}
		{
			$tmp = preg_match_all($pattern, $subject, $matches, ((($flags === null)) ? PREG_PATTERN_ORDER : $flags), $offset);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_match($pattern, $subject) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_match");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = preg_match($pattern, $subject);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_match_ex($pattern, $subject, $matches, $flags = null, $offset = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_match_ex");
		$__hx__spos = $GLOBALS['%s']->length;
		if($offset === null) {
			$offset = 0;
		}
		if($flags === null) {
			$flags = 0;
		}
		{
			$tmp = preg_match($pattern, $subject, $matches, $flags, $offset);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_quote($str, $delimiter = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_quote");
		$__hx__spos = $GLOBALS['%s']->length;
		if($delimiter === null) {
			$tmp = preg_quote($str);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = preg_quote($str, $delimiter);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_replace_callback_array($patterns_and_callbacks, $subject, $limit = null, $count = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_replace_callback_array");
		$__hx__spos = $GLOBALS['%s']->length;
		if($limit === null) {
			$limit = -1;
		}
		if($count === null) {
			$tmp = preg_replace_callback_array($patterns_and_callbacks, $subject, $limit);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = preg_replace_callback_array($patterns_and_callbacks, $subject, $limit, $count);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_replace_callback($pattern, $callback, $subject, $limit = null, $count = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_replace_callback");
		$__hx__spos = $GLOBALS['%s']->length;
		if($limit === null) {
			$limit = -1;
		}
		if($count === null) {
			$tmp = preg_replace_callback($pattern, $callback, _hx_qtype("String"), $subject, $limit);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = preg_replace_callback($pattern, $callback, _hx_qtype("String"), $subject, $limit, $count);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_replace($pattern, $replacement, $subject, $limit = null, $count = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_replace");
		$__hx__spos = $GLOBALS['%s']->length;
		if($limit === null) {
			$limit = -1;
		}
		if($count === null) {
			$tmp = preg_replace($pattern, $replacement, $subject, $limit);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = preg_replace($pattern, $replacement, $subject, $limit, $count);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function preg_split($pattern, $subject, $limit = null, $flags = null) {
		$GLOBALS['%s']->push("php.PcreNatives::preg_split");
		$__hx__spos = $GLOBALS['%s']->length;
		if($flags === null) {
			$flags = 0;
		}
		if($limit === null) {
			$limit = -1;
		}
		{
			$tmp = preg_split($pattern, $subject, $limit, $flags);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static $__properties__ = array("get_PREG_OFFSET_CAPTURE" => "get_PREG_OFFSET_CAPTURE","get_PREG_SET_ORDER" => "get_PREG_SET_ORDER","get_PREG_PATTERN_ORDER" => "get_PREG_PATTERN_ORDER");
	function __toString() { return 'php.PcreNatives'; }
}
