<?php

class haxe_io_Path {
	public function __construct($path) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("haxe.io.Path::new");
		$__hx__spos = $GLOBALS['%s']->length;
		switch($path) {
		case ".":case "..":{
			$this->dir = $path;
			$this->file = "";
			{
				$GLOBALS['%s']->pop();
				return;
			}
		}break;
		}
		$c1 = _hx_last_index_of($path, "/", null);
		$c2 = _hx_last_index_of($path, "\\", null);
		if($c1 < $c2) {
			$this->dir = _hx_substr($path, 0, $c2);
			$path = _hx_substr($path, $c2 + 1, null);
			$this->backslash = true;
		} else {
			if($c2 < $c1) {
				$this->dir = _hx_substr($path, 0, $c1);
				$path = _hx_substr($path, $c1 + 1, null);
			} else {
				$this->dir = null;
			}
		}
		$cp = _hx_last_index_of($path, ".", null);
		if($cp !== -1) {
			$this->ext = _hx_substr($path, $cp + 1, null);
			$this->file = _hx_substr($path, 0, $cp);
		} else {
			$this->ext = null;
			$this->file = $path;
		}
		$GLOBALS['%s']->pop();
	}}
	public $dir;
	public $file;
	public $ext;
	public $backslash;
	public function toString() {
		$GLOBALS['%s']->push("haxe.io.Path::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_string_or_null((haxe_io_Path_0($this))) . _hx_string_or_null($this->file) . _hx_string_or_null((haxe_io_Path_1($this)));
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
	static function withoutExtension($path) {
		$GLOBALS['%s']->push("haxe.io.Path::withoutExtension");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = new haxe_io_Path($path);
		$s->ext = null;
		{
			$tmp = $s->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function withoutDirectory($path) {
		$GLOBALS['%s']->push("haxe.io.Path::withoutDirectory");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = new haxe_io_Path($path);
		$s->dir = null;
		{
			$tmp = $s->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function directory($path) {
		$GLOBALS['%s']->push("haxe.io.Path::directory");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = new haxe_io_Path($path);
		if($s->dir === null) {
			$GLOBALS['%s']->pop();
			return "";
		}
		{
			$tmp = $s->dir;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function join($paths) {
		$GLOBALS['%s']->push("haxe.io.Path::join");
		$__hx__spos = $GLOBALS['%s']->length;
		$paths1 = $paths->filter(array(new _hx_lambda(array(&$paths), "haxe_io_Path_2"), 'execute'));
		if($paths1->length === 0) {
			$GLOBALS['%s']->pop();
			return "";
		}
		$path = $paths1[0];
		{
			$_g1 = 1;
			$_g = $paths1->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$path = haxe_io_Path::addTrailingSlash($path);
				$path .= _hx_string_or_null($paths1[$i]);
				unset($i);
			}
		}
		{
			$tmp = haxe_io_Path::normalize($path);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function normalize($path) {
		$GLOBALS['%s']->push("haxe.io.Path::normalize");
		$__hx__spos = $GLOBALS['%s']->length;
		$slash = "/";
		$path = _hx_explode("\\", $path)->join("/");
		if($path === null || $path === $slash) {
			$GLOBALS['%s']->pop();
			return $slash;
		}
		$target = (new _hx_array(array()));
		{
			$_g = 0;
			$_g1 = _hx_explode($slash, $path);
			while($_g < $_g1->length) {
				$token = $_g1[$_g];
				++$_g;
				if($token === ".." && $target->length > 0 && $target[$target->length - 1] !== "..") {
					$target->pop();
				} else {
					if($token !== ".") {
						$target->push($token);
					}
				}
				unset($token);
			}
		}
		$tmp = $target->join($slash);
		$regex = new EReg("([^:])/+", "g");
		$result = $regex->replace($tmp, "\$1" . _hx_string_or_null($slash));
		$acc = new StringBuf();
		$colon = false;
		$slashes = false;
		{
			$_g11 = 0;
			$_g2 = strlen($tmp);
			while($_g11 < $_g2) {
				$i = $_g11++;
				{
					$_g21 = _hx_char_code_at($tmp, $i);
					{
						$i1 = $_g21;
						if($_g21 !== null) {
							switch($_g21) {
							case 58:{
								$acc->add(":");
								$colon = true;
							}break;
							case 47:{
								if($colon === false) {
									$slashes = true;
								} else {
									$colon = false;
									if($slashes) {
										$acc->add("/");
										$slashes = false;
									}
									$acc->add(chr($i1));
								}
							}break;
							default:{
								$colon = false;
								if($slashes) {
									$acc->add("/");
									$slashes = false;
								}
								$acc->add(chr($i1));
							}break;
							}
						} else {
							$colon = false;
							if($slashes) {
								$acc->add("/");
								$slashes = false;
							}
							$acc->add(chr($i1));
						}
						unset($i1);
					}
					unset($_g21);
				}
				unset($i);
			}
		}
		$result1 = $acc->b;
		{
			$GLOBALS['%s']->pop();
			return $result1;
		}
		$GLOBALS['%s']->pop();
	}
	static function addTrailingSlash($path) {
		$GLOBALS['%s']->push("haxe.io.Path::addTrailingSlash");
		$__hx__spos = $GLOBALS['%s']->length;
		if(strlen($path) === 0) {
			$GLOBALS['%s']->pop();
			return "/";
		}
		$c1 = _hx_last_index_of($path, "/", null);
		$c2 = _hx_last_index_of($path, "\\", null);
		if($c1 < $c2) {
			if($c2 !== strlen($path) - 1) {
				$tmp = _hx_string_or_null($path) . "\\";
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$GLOBALS['%s']->pop();
				return $path;
			}
		} else {
			if($c1 !== strlen($path) - 1) {
				$tmp = _hx_string_or_null($path) . "/";
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$GLOBALS['%s']->pop();
				return $path;
			}
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
function haxe_io_Path_0(&$__hx__this) {
	if($__hx__this->dir === null) {
		return "";
	} else {
		return _hx_string_or_null($__hx__this->dir) . _hx_string_or_null(((($__hx__this->backslash) ? "\\" : "/")));
	}
}
function haxe_io_Path_1(&$__hx__this) {
	if($__hx__this->ext === null) {
		return "";
	} else {
		return "." . _hx_string_or_null($__hx__this->ext);
	}
}
function haxe_io_Path_2(&$paths, $s) {
	{
		$GLOBALS['%s']->push("haxe.io.Path::join@190");
		$__hx__spos2 = $GLOBALS['%s']->length;
		{
			$tmp = $s !== null && $s !== "";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
}
