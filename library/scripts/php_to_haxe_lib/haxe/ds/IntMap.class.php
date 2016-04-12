<?php

class haxe_ds_IntMap implements haxe_IMap, IteratorAggregate{
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("haxe.ds.IntMap::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->h = array();
		$GLOBALS['%s']->pop();
	}}
	public $h;
	public function set($key, $value) {
		$GLOBALS['%s']->push("haxe.ds.IntMap::set");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->h[$key] = $value;
		$GLOBALS['%s']->pop();
	}
	public function get($key) {
		$GLOBALS['%s']->push("haxe.ds.IntMap::get");
		$__hx__spos = $GLOBALS['%s']->length;
		if(array_key_exists($key, $this->h)) {
			$tmp = $this->h[$key];
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	public function keys() {
		$GLOBALS['%s']->push("haxe.ds.IntMap::keys");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new _hx_array_iterator(array_keys($this->h));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function iterator() {
		$GLOBALS['%s']->push("haxe.ds.IntMap::iterator");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new _hx_array_iterator(array_values($this->h));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getIterator() {
		$GLOBALS['%s']->push("haxe.ds.IntMap::getIterator");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->iterator();
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
	function __toString() { return 'haxe.ds.IntMap'; }
}
