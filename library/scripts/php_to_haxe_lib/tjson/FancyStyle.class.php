<?php

class tjson_FancyStyle implements tjson_EncodeStyle{
	public function __construct($tab = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("tjson.FancyStyle::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($tab === null) {
			$tab = "    ";
		}
		$this->tab = $tab;
		$this->charTimesNCache = (new _hx_array(array("")));
		$GLOBALS['%s']->pop();
	}}
	public $tab;
	public function beginObject($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::beginObject");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "{\x0A";
		}
		$GLOBALS['%s']->pop();
	}
	public function endObject($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::endObject");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = "\x0A" . _hx_string_or_null($this->charTimesN($depth)) . "}";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function beginArray($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::beginArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return "[\x0A";
		}
		$GLOBALS['%s']->pop();
	}
	public function endArray($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::endArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = "\x0A" . _hx_string_or_null($this->charTimesN($depth)) . "]";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function firstEntry($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::firstEntry");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_string_or_null($this->charTimesN($depth + 1)) . " ";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function entrySeperator($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::entrySeperator");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = "\x0A" . _hx_string_or_null($this->charTimesN($depth + 1)) . ",";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function keyValueSeperator($depth) {
		$GLOBALS['%s']->push("tjson.FancyStyle::keyValueSeperator");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return " : ";
		}
		$GLOBALS['%s']->pop();
	}
	public $charTimesNCache;
	public function charTimesN($n) {
		$GLOBALS['%s']->push("tjson.FancyStyle::charTimesN");
		$__hx__spos = $GLOBALS['%s']->length;
		if($n < $this->charTimesNCache->length) {
			$tmp = $this->charTimesNCache[$n];
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = $this->charTimesNCache[$n] = _hx_string_or_null($this->charTimesN($n - 1)) . _hx_string_or_null($this->tab);
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
	function __toString() { return 'tjson.FancyStyle'; }
}
