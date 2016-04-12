<?php

class php_VarNatives {
	public function __construct(){}
	static function boolval($var_) {
		$GLOBALS['%s']->push("php.VarNatives::boolval");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = boolval($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function debug_zval_dump($variable, $restArgs = null) {
		$GLOBALS['%s']->push("php.VarNatives::debug_zval_dump");
		$__hx__spos = $GLOBALS['%s']->length;
		if($restArgs === null) {
			debug_zval_dump($variable);
		} else {
			debug_zval_dump($variable, $restArgs);
		}
		$GLOBALS['%s']->pop();
	}
	static function hempty($var_) {
		$GLOBALS['%s']->push("php.VarNatives::empty");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = empty($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function floatval($var_) {
		$GLOBALS['%s']->push("php.VarNatives::floatval");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = floatval($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function get_defined_vars() {
		$GLOBALS['%s']->push("php.VarNatives::get_defined_vars");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = get_defined_vars();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function get_resource_type($handle) {
		$GLOBALS['%s']->push("php.VarNatives::get_resource_type");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = get_resource_type($handle);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function gettype($var_) {
		$GLOBALS['%s']->push("php.VarNatives::gettype");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = gettype($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function import_request_variables($types, $prefix = null) {
		$GLOBALS['%s']->push("php.VarNatives::import_request_variables");
		$__hx__spos = $GLOBALS['%s']->length;
		if($prefix === null) {
			$tmp = import_request_variables($types);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = import_request_variables($types, $prefix);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function intval($var_, $base = null) {
		$GLOBALS['%s']->push("php.VarNatives::intval");
		$__hx__spos = $GLOBALS['%s']->length;
		if($base === null) {
			$base = 10;
		}
		{
			$tmp = intval($var_, $base);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_array($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_array");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_array($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_bool($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_bool");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_bool($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_callable($var_, $syntax_only = null, $callable_name = null) {
		$GLOBALS['%s']->push("php.VarNatives::is_callable");
		$__hx__spos = $GLOBALS['%s']->length;
		if($syntax_only === null) {
			$syntax_only = false;
		}
		if($callable_name === null) {
			$tmp = is_callable($var_, $syntax_only);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = is_callable($var_, $syntax_only, $callable_name);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_float($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_float");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_float($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_int($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_int");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_int($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_null($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_null");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_null($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_numeric($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_numeric");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_numeric($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_object($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_object");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_object($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_resource($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_resource");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_resource($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_scalar($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_scalar");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_scalar($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function is_string($var_) {
		$GLOBALS['%s']->push("php.VarNatives::is_string");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = is_string($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hisset($var_) {
		$GLOBALS['%s']->push("php.VarNatives::isset");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = isset($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function print_r($expression, $return_ = null) {
		$GLOBALS['%s']->push("php.VarNatives::print_r");
		$__hx__spos = $GLOBALS['%s']->length;
		if($return_ === null) {
			$return_ = false;
		}
		{
			$tmp = print_r($expression, $return_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function serialize($value) {
		$GLOBALS['%s']->push("php.VarNatives::serialize");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = serialize($value);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function settype($var_, $type) {
		$GLOBALS['%s']->push("php.VarNatives::settype");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = settype($var_, $type);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function strval($var_) {
		$GLOBALS['%s']->push("php.VarNatives::strval");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = strval($var_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function unserialize($str, $options = null) {
		$GLOBALS['%s']->push("php.VarNatives::unserialize");
		$__hx__spos = $GLOBALS['%s']->length;
		if($options === null) {
			$tmp = unserialize($str);
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = unserialize($str, $options);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function hunset($var_) {
		$GLOBALS['%s']->push("php.VarNatives::unset");
		$__hx__spos = $GLOBALS['%s']->length;
		unset($var_);
		{
			$GLOBALS['%s']->pop();
			return;
		}
		$GLOBALS['%s']->pop();
	}
	static function var_dump($expression, $restArgs = null) {
		$GLOBALS['%s']->push("php.VarNatives::var_dump");
		$__hx__spos = $GLOBALS['%s']->length;
		if($restArgs === null) {
			var_dump($expression);
		} else {
			var_dump($expression, $restArgs);
		}
		$GLOBALS['%s']->pop();
	}
	static function var_export($expression, $return_ = null) {
		$GLOBALS['%s']->push("php.VarNatives::var_export");
		$__hx__spos = $GLOBALS['%s']->length;
		if($return_ === null) {
			$return_ = false;
		}
		{
			$tmp = var_export($expression, $return_);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'php.VarNatives'; }
}
