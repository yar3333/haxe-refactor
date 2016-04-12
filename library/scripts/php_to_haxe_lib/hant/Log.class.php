<?php

class hant_Log {
	public function __construct($depthLimit = null, $levelLimit = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("hant.Log::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($levelLimit === null) {
			$levelLimit = 2147483647;
		}
		if($depthLimit === null) {
			$depthLimit = 2147483647;
		}
		$this->depthLimit = $depthLimit;
		$this->levelLimit = $levelLimit;
		$this->depth = -1;
		$this->ind = 0;
		$this->inBlock = false;
		$this->shown = (new _hx_array(array()));
		$GLOBALS['%s']->pop();
	}}
	public $depthLimit;
	public $levelLimit;
	public $depth;
	public $ind;
	public $inBlock;
	public $shown;
	public function startInner($message, $level) {
		$GLOBALS['%s']->push("hant.Log::startInner");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->depth++;
		if($this->depth < $this->depthLimit) {
			if($level <= $this->levelLimit) {
				if($this->inBlock) {
					$this->println("");
				}
				$this->hprint(_hx_string_or_null($this->indent($this->ind)) . _hx_string_or_null($message) . ": ");
				$this->inBlock = true;
				$this->shown->push(true);
				$this->ind++;
			} else {
				$this->shown->push(false);
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function finishSuccessInner($text) {
		$GLOBALS['%s']->push("hant.Log::finishSuccessInner");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->depth < $this->depthLimit) {
			if($this->shown->pop()) {
				$text = Std::string($text);
				if(!$this->inBlock) {
					$this->hprint($this->indent($this->ind));
				}
				$this->ind--;
				if(_hx_index_of($text, "\x0A", null) < 0) {
					$this->println("[" . _hx_string_or_null($text) . "]");
				} else {
					$this->println("\x0A" . _hx_string_or_null($this->indent($this->ind + 1)) . "[\x0A" . _hx_string_or_null($this->indent($this->ind + 2)) . _hx_string_or_null(hant_Log_0($this, $text)) . "\x0A" . _hx_string_or_null($this->indent($this->ind + 1)) . "]");
				}
				$this->inBlock = false;
			}
		}
		$this->depth--;
		$GLOBALS['%s']->pop();
	}
	public function finishFailInner($text) {
		$GLOBALS['%s']->push("hant.Log::finishFailInner");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->depth < $this->depthLimit) {
			if($this->shown->pop()) {
				$text = Std::string($text);
				if(!$this->inBlock) {
					$this->hprint($this->indent($this->ind));
				}
				$this->ind--;
				if(_hx_index_of($text, "\x0A", null) < 0) {
					$this->println("[" . _hx_string_or_null($text) . "]");
				} else {
					$this->println("\x0A" . _hx_string_or_null($this->indent($this->ind + 1)) . "[\x0A" . _hx_string_or_null($this->indent($this->ind + 2)) . _hx_string_or_null(hant_Log_1($this, $text)) . "\x0A" . _hx_string_or_null($this->indent($this->ind + 1)) . "]");
				}
				$this->inBlock = false;
			}
		}
		$this->depth--;
		$GLOBALS['%s']->pop();
	}
	public function echoInner($text, $level) {
		$GLOBALS['%s']->push("hant.Log::echoInner");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->depth < $this->depthLimit) {
			if($level <= $this->levelLimit) {
				$text = Std::string($text);
				if($this->inBlock) {
					$this->println("");
				}
				$this->println(_hx_string_or_null($this->indent($this->ind)) . _hx_string_or_null(hant_Log_2($this, $level, $text)));
				$this->inBlock = false;
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function indent($depth) {
		$GLOBALS['%s']->push("hant.Log::indent");
		$__hx__spos = $GLOBALS['%s']->length;
		$l = $depth * 2;
		if(strlen(" ") === 0 || strlen("") >= $l) {
			$GLOBALS['%s']->pop();
			return "";
		} else {
			$tmp = str_pad("", Math::ceil(($l - strlen("")) / strlen(" ")) * strlen(" ") + strlen(""), " ", STR_PAD_RIGHT);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function hprint($s) {
		$GLOBALS['%s']->push("hant.Log::print");
		$__hx__spos = $GLOBALS['%s']->length;
		Sys::hprint($s);
		$GLOBALS['%s']->pop();
	}
	public function println($s) {
		$GLOBALS['%s']->push("hant.Log::println");
		$__hx__spos = $GLOBALS['%s']->length;
		Sys::println($s);
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
	static $instance;
	static function start($message, $level = null) {
		$GLOBALS['%s']->push("hant.Log::start");
		$__hx__spos = $GLOBALS['%s']->length;
		if($level === null) {
			$level = 1;
		}
		if(hant_Log::$instance !== null) {
			hant_Log::$instance->startInner($message, $level);
		}
		$GLOBALS['%s']->pop();
	}
	static function finishSuccess($text = null) {
		$GLOBALS['%s']->push("hant.Log::finishSuccess");
		$__hx__spos = $GLOBALS['%s']->length;
		if($text === null) {
			$text = "OK";
		}
		if(hant_Log::$instance !== null) {
			hant_Log::$instance->finishSuccessInner($text);
		}
		$GLOBALS['%s']->pop();
	}
	static function finishFail($text = null) {
		$GLOBALS['%s']->push("hant.Log::finishFail");
		$__hx__spos = $GLOBALS['%s']->length;
		if($text === null) {
			$text = "FAIL";
		}
		if(hant_Log::$instance !== null) {
			hant_Log::$instance->finishFailInner($text);
		}
		$GLOBALS['%s']->pop();
	}
	static function hecho($message, $level = null) {
		$GLOBALS['%s']->push("hant.Log::echo");
		$__hx__spos = $GLOBALS['%s']->length;
		if($level === null) {
			$level = 1;
		}
		if(hant_Log::$instance !== null) {
			hant_Log::$instance->echoInner($message, $level);
		}
		$GLOBALS['%s']->pop();
	}
	static function process($message, $level = null, $procFunc) {
		$GLOBALS['%s']->push("hant.Log::process");
		$__hx__spos = $GLOBALS['%s']->length;
		if($level === null) {
			$level = 1;
		}
		hant_Log::start($message, $level);
		try {
			call_user_func($procFunc);
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			$e = $_ex_;
			{
				$GLOBALS['%e'] = (new _hx_array(array()));
				while($GLOBALS['%s']->length >= $__hx__spos) {
					$GLOBALS['%e']->unshift($GLOBALS['%s']->pop());
				}
				$GLOBALS['%s']->push($GLOBALS['%e'][0]);
				hant_Log::finishFail(null);
				stdlib_Exception::rethrow($e);
			}
		}
		hant_Log::finishSuccess(null);
		$GLOBALS['%s']->pop();
	}
	static function processResult($message, $level = null, $procFunc) {
		$GLOBALS['%s']->push("hant.Log::processResult");
		$__hx__spos = $GLOBALS['%s']->length;
		if($level === null) {
			$level = 1;
		}
		hant_Log::start($message, $level);
		$r = null;
		try {
			$r = call_user_func($procFunc);
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			$e = $_ex_;
			{
				$GLOBALS['%e'] = (new _hx_array(array()));
				while($GLOBALS['%s']->length >= $__hx__spos) {
					$GLOBALS['%e']->unshift($GLOBALS['%s']->pop());
				}
				$GLOBALS['%s']->push($GLOBALS['%e'][0]);
				hant_Log::finishFail(null);
				stdlib_Exception::rethrow($e);
			}
		}
		hant_Log::finishSuccess(null);
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'hant.Log'; }
}
hant_Log::$instance = new hant_Log(null, null);
function hant_Log_0(&$__hx__this, &$text) {
	{
		$by = "\x0A" . _hx_string_or_null($__hx__this->indent($__hx__this->ind + 2));
		return str_replace("\x0A", $by, $text);
	}
}
function hant_Log_1(&$__hx__this, &$text) {
	{
		$by = "\x0A" . _hx_string_or_null($__hx__this->indent($__hx__this->ind + 2));
		return str_replace("\x0A", $by, $text);
	}
}
function hant_Log_2(&$__hx__this, &$level, &$text) {
	{
		$by = "\x0A" . _hx_string_or_null($__hx__this->indent($__hx__this->ind));
		return str_replace("\x0A", $by, $text);
	}
}
