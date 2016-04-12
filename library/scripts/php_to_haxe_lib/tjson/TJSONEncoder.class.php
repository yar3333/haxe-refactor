<?php

class tjson_TJSONEncoder {
	public function __construct($useCache = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($useCache === null) {
			$useCache = true;
		}
		$this->uCache = $useCache;
		if($this->uCache) {
			$this->cache = new _hx_array(array());
		}
		$GLOBALS['%s']->pop();
	}}
	public $cache;
	public $uCache;
	public function doEncode($obj, $style = null) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::doEncode");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!Reflect::isObject($obj)) {
			throw new HException("Provided object is not an object.");
		}
		$st = null;
		if(Std::is($style, _hx_qtype("tjson.EncodeStyle"))) {
			$st = $style;
		} else {
			if(_hx_equal($style, "fancy")) {
				$st = new tjson_FancyStyle(null);
			} else {
				$st = new tjson_SimpleStyle();
			}
		}
		$buffer = new StringBuf();
		if(Std::is($obj, _hx_qtype("Array")) || Std::is($obj, _hx_qtype("List"))) {
			$buffer->add($this->encodeIterable($obj, $st, 0));
		} else {
			if(Std::is($obj, _hx_qtype("haxe.ds.StringMap"))) {
				$buffer->add($this->encodeMap($obj, $st, 0));
			} else {
				$this->cacheEncode($obj);
				$buffer->add($this->encodeObject($obj, $st, 0));
			}
		}
		{
			$tmp = $buffer->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function encodeObject($obj, $style, $depth) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::encodeObject");
		$__hx__spos = $GLOBALS['%s']->length;
		$buffer = new StringBuf();
		$buffer->add($style->beginObject($depth));
		$fieldCount = 0;
		$fields = null;
		$dontEncodeFields = null;
		$cls = Type::getClass($obj);
		if($cls !== null) {
			$fields = Type::getInstanceFields($cls);
		} else {
			$fields = Reflect::fields($obj);
		}
		{
			$_g = Type::typeof($obj);
			switch($_g->index) {
			case 6:{
				$c = _hx_deref($_g)->params[0];
				{
					if($fieldCount++ > 0) {
						$buffer->add($style->entrySeperator($depth));
					} else {
						$buffer->add($style->firstEntry($depth));
					}
					$buffer->add("\"_hxcls\"" . _hx_string_or_null($style->keyValueSeperator($depth)));
					$buffer->add($this->encodeValue(Type::getClassName($c), $style, $depth));
					if(_hx_field($obj, "TJ_noEncode") !== null) {
						$dontEncodeFields = $obj->TJ_noEncode();
					}
				}
			}break;
			default:{}break;
			}
		}
		{
			$_g1 = 0;
			while($_g1 < $fields->length) {
				$field = $fields[$_g1];
				++$_g1;
				if($dontEncodeFields !== null && $dontEncodeFields->indexOf($field, null) >= 0) {
					continue;
				}
				$value = Reflect::field($obj, $field);
				$vStr = $this->encodeValue($value, $style, $depth);
				if($vStr !== null) {
					if($fieldCount++ > 0) {
						$buffer->add($style->entrySeperator($depth));
					} else {
						$buffer->add($style->firstEntry($depth));
					}
					$buffer->add("\"" . _hx_string_or_null($field) . "\"" . _hx_string_or_null($style->keyValueSeperator($depth)) . _hx_string_or_null($vStr));
				}
				unset($value,$vStr,$field);
			}
		}
		$buffer->add($style->endObject($depth));
		{
			$tmp = $buffer->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function encodeMap($obj, $style, $depth) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::encodeMap");
		$__hx__spos = $GLOBALS['%s']->length;
		$buffer = new StringBuf();
		$buffer->add($style->beginObject($depth));
		$fieldCount = 0;
		if(null == $obj) throw new HException('null iterable');
		$__hx__it = $obj->keys();
		while($__hx__it->hasNext()) {
			unset($field);
			$field = $__hx__it->next();
			if($fieldCount++ > 0) {
				$buffer->add($style->entrySeperator($depth));
			} else {
				$buffer->add($style->firstEntry($depth));
			}
			$value = $obj->get($field);
			$buffer->add("\"" . _hx_string_or_null($field) . "\"" . _hx_string_or_null($style->keyValueSeperator($depth)));
			$buffer->add($this->encodeValue($value, $style, $depth));
			unset($value);
		}
		$buffer->add($style->endObject($depth));
		{
			$tmp = $buffer->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function encodeIterable($obj, $style, $depth) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::encodeIterable");
		$__hx__spos = $GLOBALS['%s']->length;
		$buffer = new StringBuf();
		$buffer->add($style->beginArray($depth));
		$fieldCount = 0;
		if(null == $obj) throw new HException('null iterable');
		$__hx__it = $obj->iterator();
		while($__hx__it->hasNext()) {
			unset($value);
			$value = $__hx__it->next();
			if($fieldCount++ > 0) {
				$buffer->add($style->entrySeperator($depth));
			} else {
				$buffer->add($style->firstEntry($depth));
			}
			$buffer->add($this->encodeValue($value, $style, $depth));
		}
		$buffer->add($style->endArray($depth));
		{
			$tmp = $buffer->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function cacheEncode($value) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::cacheEncode");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!$this->uCache) {
			$GLOBALS['%s']->pop();
			return null;
		}
		{
			$_g1 = 0;
			$_g = $this->cache->length;
			while($_g1 < $_g) {
				$c = $_g1++;
				if(_hx_equal($this->cache[$c], $value)) {
					$tmp = "\"" . _hx_string_or_null(tjson_TJSON::$OBJECT_REFERENCE_PREFIX) . _hx_string_rec($c, "") . "\"";
					$GLOBALS['%s']->pop();
					return $tmp;
					unset($tmp);
				}
				unset($c);
			}
		}
		$this->cache->push($value);
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	public function encodeValue($value, $style, $depth) {
		$GLOBALS['%s']->push("tjson.TJSONEncoder::encodeValue");
		$__hx__spos = $GLOBALS['%s']->length;
		if(Std::is($value, _hx_qtype("Int")) || Std::is($value, _hx_qtype("Float"))) {
			$GLOBALS['%s']->pop();
			return $value;
		} else {
			if(Std::is($value, _hx_qtype("Array")) || Std::is($value, _hx_qtype("List"))) {
				$v = $value;
				{
					$tmp = $this->encodeIterable($v, $style, $depth + 1);
					$GLOBALS['%s']->pop();
					return $tmp;
				}
			} else {
				if(Std::is($value, _hx_qtype("List"))) {
					$v1 = $value;
					{
						$tmp = $this->encodeIterable($v1, $style, $depth + 1);
						$GLOBALS['%s']->pop();
						return $tmp;
					}
				} else {
					if(Std::is($value, _hx_qtype("haxe.ds.StringMap"))) {
						$tmp = $this->encodeMap($value, $style, $depth + 1);
						$GLOBALS['%s']->pop();
						return $tmp;
					} else {
						if(Std::is($value, _hx_qtype("String"))) {
							$tmp = "\"" . _hx_string_or_null(tjson_TJSONEncoder_0($this, $depth, $style, $value)) . "\"";
							$GLOBALS['%s']->pop();
							return $tmp;
						} else {
							if(Std::is($value, _hx_qtype("Bool"))) {
								$GLOBALS['%s']->pop();
								return $value;
							} else {
								if(Reflect::isObject($value)) {
									$ret = $this->cacheEncode($value);
									if($ret !== null) {
										$GLOBALS['%s']->pop();
										return $ret;
									}
									{
										$tmp = $this->encodeObject($value, $style, $depth + 1);
										$GLOBALS['%s']->pop();
										return $tmp;
									}
								} else {
									if($value === null) {
										$GLOBALS['%s']->pop();
										return "null";
									} else {
										$GLOBALS['%s']->pop();
										return null;
									}
								}
							}
						}
					}
				}
			}
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
	function __toString() { return 'tjson.TJSONEncoder'; }
}
function tjson_TJSONEncoder_0(&$__hx__this, &$depth, &$style, &$value) {
	{
		$s = null;
		{
			$s1 = null;
			{
				$s2 = null;
				{
					$s3 = Std::string($value);
					$s2 = str_replace("\\", "\\\\", $s3);
				}
				$s1 = str_replace("\x0A", "\\n", $s2);
			}
			$s = str_replace("\x0D", "\\r", $s1);
		}
		return str_replace("\"", "\\\"", $s);
	}
}
