<?php

class hant_CmdOptions {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("hant.CmdOptions::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->options = (new _hx_array(array()));
		$GLOBALS['%s']->pop();
	}}
	public $options;
	public $args;
	public $paramWoSwitchIndex;
	public $params;
	public function get($name) {
		$GLOBALS['%s']->push("hant.CmdOptions::get");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->params->get($name);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function add($name, $defaultValue, $switches = null, $help = null) {
		$GLOBALS['%s']->push("hant.CmdOptions::add");
		$__hx__spos = $GLOBALS['%s']->length;
		if($help === null) {
			$help = "";
		}
		$type = Type::typeof($defaultValue);
		if($type === ValueType::$TNull) {
			$type = ValueType::TClass(_hx_qtype("String"));
		}
		$this->addInner($name, $defaultValue, $type, $switches, $help, false);
		$GLOBALS['%s']->pop();
	}
	public function addRepeatable($name, $clas, $switches = null, $help = null) {
		$GLOBALS['%s']->push("hant.CmdOptions::addRepeatable");
		$__hx__spos = $GLOBALS['%s']->length;
		if($help === null) {
			$help = "";
		}
		$className = Type::getClassName($clas);
		$type = null;
		switch($className) {
		case "String":{
			$type = ValueType::TClass(_hx_qtype("String"));
		}break;
		case "Int":{
			$type = ValueType::$TInt;
		}break;
		case "Float":{
			$type = ValueType::$TFloat;
		}break;
		default:{
			throw new HException("Type '" . _hx_string_or_null($className) . "' can not be used for repeatable option '" . _hx_string_or_null($name) . "'.");
		}break;
		}
		$this->addInner($name, (new _hx_array(array())), $type, $switches, $help, true);
		$GLOBALS['%s']->pop();
	}
	public function addInner($name, $defaultValue, $type, $switches, $help, $repeatable) {
		$GLOBALS['%s']->push("hant.CmdOptions::addInner");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!$this->hasOption($name)) {
			$this->options->push(_hx_anonymous(array("name" => $name, "defaultValue" => $defaultValue, "type" => $type, "switches" => $switches, "help" => $help, "repeatable" => $repeatable)));
		} else {
			throw new HException("Option '" . _hx_string_or_null($name) . "' already added.");
		}
		$GLOBALS['%s']->pop();
	}
	public function getHelpMessage($prefix = null) {
		$GLOBALS['%s']->push("hant.CmdOptions::getHelpMessage");
		$__hx__spos = $GLOBALS['%s']->length;
		if($prefix === null) {
			$prefix = "\x09";
		}
		$maxSwitchLength = 0;
		{
			$_g = 0;
			$_g1 = $this->options;
			while($_g < $_g1->length) {
				$opt = $_g1[$_g];
				++$_g;
				if($opt->switches !== null && $opt->switches->length > 0) {
					$b = strlen($opt->switches->join(", "));
					if($maxSwitchLength > $b) {
						$maxSwitchLength = $maxSwitchLength;
					} else {
						$maxSwitchLength = $b;
					}
					unset($b);
				} else {
					$b1 = strlen($opt->name) + 2;
					if($maxSwitchLength > $b1) {
						$maxSwitchLength = $maxSwitchLength;
					} else {
						$maxSwitchLength = $b1;
					}
					unset($b1);
				}
				unset($opt);
			}
		}
		$s = "";
		{
			$_g2 = 0;
			$_g11 = $this->options;
			while($_g2 < $_g11->length) {
				$opt1 = $_g11[$_g2];
				++$_g2;
				if($opt1->switches !== null && $opt1->switches->length > 0) {
					$s .= _hx_string_or_null($prefix) . _hx_string_or_null(hant_CmdOptions_0($this, $_g11, $_g2, $maxSwitchLength, $opt1, $prefix, $s));
				} else {
					$s .= _hx_string_or_null($prefix) . _hx_string_or_null(hant_CmdOptions_1($this, $_g11, $_g2, $maxSwitchLength, $opt1, $prefix, $s));
				}
				if($opt1->help !== null && $opt1->help !== "") {
					$helpLines = _hx_explode("\x0A", $opt1->help);
					$s .= _hx_string_or_null($helpLines->shift()) . "\x0A";
					$s .= _hx_string_or_null(Lambda::map($helpLines, array(new _hx_lambda(array(&$_g11, &$_g2, &$helpLines, &$maxSwitchLength, &$opt1, &$prefix, &$s), "hant_CmdOptions_2"), 'execute'))->join(""));
					unset($helpLines);
				} else {
					$s .= "\x0A";
				}
				$s .= "\x0A";
				unset($opt1);
			}
		}
		{
			$tmp = _hx_string_or_null(stdlib_StringTools::rtrim($s, null)) . "\x0A";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function parse($args) {
		$GLOBALS['%s']->push("hant.CmdOptions::parse");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->args = $args->copy();
		$this->paramWoSwitchIndex = 0;
		$this->params = new haxe_ds_StringMap();
		{
			$_g = 0;
			$_g1 = $this->options;
			while($_g < $_g1->length) {
				$opt = $_g1[$_g];
				++$_g;
				{
					$value = $opt->defaultValue;
					$this->params->set($opt->name, $value);
					unset($value);
				}
				unset($opt);
			}
		}
		while($this->args->length > 0) {
			$this->parseElement();
		}
		{
			$tmp = $this->params;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function parseElement() {
		$GLOBALS['%s']->push("hant.CmdOptions::parseElement");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = $this;
		$arg = $this->args->shift();
		if($arg !== "--") {
			if(_hx_substr($arg, 0, 1) === "-" && $arg !== "-") {
				$arg = _hx_deref(new EReg("^(--?.+)=(.+)\$", ""))->map($arg, array(new _hx_lambda(array(&$_g, &$arg), "hant_CmdOptions_3"), 'execute'));
				{
					$_g1 = 0;
					$_g11 = $this->options;
					while($_g1 < $_g11->length) {
						$opt = $_g11[$_g1];
						++$_g1;
						if($opt->switches !== null) {
							$_g2 = 0;
							$_g3 = $opt->switches;
							while($_g2 < $_g3->length) {
								$s = $_g3[$_g2];
								++$_g2;
								if($s === $arg) {
									$this->parseValue($opt, $arg);
									{
										$GLOBALS['%s']->pop();
										return;
									}
								}
								unset($s);
							}
							unset($_g3,$_g2);
						}
						unset($opt);
					}
				}
				throw new HException("Unknow switch '" . _hx_string_or_null($arg) . "'.");
			} else {
				$this->args->unshift($arg);
				$this->parseValue($this->getNextNoSwitchOption(), $this->args[0]);
			}
		} else {
			while($this->args->length > 0) {
				$this->parseValue($this->getNextNoSwitchOption(), $this->args[0]);
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function parseValue($opt, $s) {
		$GLOBALS['%s']->push("hant.CmdOptions::parseValue");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$_g = $opt->type;
			switch($_g->index) {
			case 1:{
				$this->ensureValueExist($s);
				if(!$opt->repeatable) {
					$value = stdlib_Std::parseInt($this->args->shift(), null);
					$this->params->set($opt->name, $value);
				} else {
					$this->params->get($opt->name)->push(stdlib_Std::parseInt($this->args->shift(), null));
				}
			}break;
			case 2:{
				$this->ensureValueExist($s);
				if(!$opt->repeatable) {
					$value1 = stdlib_Std::parseFloat($this->args->shift(), null);
					$this->params->set($opt->name, $value1);
				} else {
					$this->params->get($opt->name)->push(stdlib_Std::parseFloat($this->args->shift(), null));
				}
			}break;
			case 3:{
				$this->params->set($opt->name, !$opt->defaultValue);
			}break;
			case 6:{
				$c = _hx_deref($_g)->params[0];
				if((is_object($_t = $c) && !($_t instanceof Enum) ? $_t === _hx_qtype("String") : $_t == _hx_qtype("String"))) {
					$this->ensureValueExist($s);
					if(!$opt->repeatable) {
						$value2 = $this->args->shift();
						$this->params->set($opt->name, $value2);
					} else {
						$this->params->get($opt->name)->push($this->args->shift());
					}
				} else {
					throw new HException("Option type of class '" . _hx_string_or_null(Type::getClassName($c)) . "' not supported.");
				}
			}break;
			default:{
				throw new HException("Option type '" . Std::string($opt->type) . "' not supported.");
			}break;
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function hasOption($name) {
		$GLOBALS['%s']->push("hant.CmdOptions::hasOption");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Lambda::exists($this->options, array(new _hx_lambda(array(&$name), "hant_CmdOptions_4"), 'execute'));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function ensureValueExist($s) {
		$GLOBALS['%s']->push("hant.CmdOptions::ensureValueExist");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->args->length === 0) {
			throw new HException("Missing value after '" . _hx_string_or_null($s) . "' switch.");
		}
		$GLOBALS['%s']->pop();
	}
	public function getNextNoSwitchOption() {
		$GLOBALS['%s']->push("hant.CmdOptions::getNextNoSwitchOption");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$_g1 = $this->paramWoSwitchIndex;
			$_g = $this->options->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if(_hx_array_get($this->options, $i)->switches === null) {
					if(!_hx_array_get($this->options, $i)->repeatable) {
						$this->paramWoSwitchIndex = $i + 1;
					}
					{
						$tmp = $this->options[$i];
						$GLOBALS['%s']->pop();
						return $tmp;
						unset($tmp);
					}
				}
				unset($i);
			}
		}
		throw new HException("Unexpected argument '" . _hx_string_or_null($this->args[0]) . "'.");
		{
			$GLOBALS['%s']->pop();
			return null;
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
	function __toString() { return 'hant.CmdOptions'; }
}
function hant_CmdOptions_0(&$__hx__this, &$_g11, &$_g2, &$maxSwitchLength, &$opt1, &$prefix, &$s) {
	{
		$s1 = $opt1->switches->join(", ");
		$l = $maxSwitchLength + 1;
		if(strlen(" ") === 0 || strlen($s1) >= $l) {
			return $s1;
		} else {
			return str_pad($s1, Math::ceil(($l - strlen($s1)) / strlen(" ")) * strlen(" ") + strlen($s1), " ", STR_PAD_RIGHT);
		}
		unset($s1,$l);
	}
}
function hant_CmdOptions_1(&$__hx__this, &$_g11, &$_g2, &$maxSwitchLength, &$opt1, &$prefix, &$s) {
	{
		$s2 = "<" . _hx_string_or_null($opt1->name) . ">";
		$l1 = $maxSwitchLength + 1;
		if(strlen(" ") === 0 || strlen($s2) >= $l1) {
			return $s2;
		} else {
			return str_pad($s2, Math::ceil(($l1 - strlen($s2)) / strlen(" ")) * strlen(" ") + strlen($s2), " ", STR_PAD_RIGHT);
		}
		unset($s2,$l1);
	}
}
function hant_CmdOptions_2(&$_g11, &$_g2, &$helpLines, &$maxSwitchLength, &$opt1, &$prefix, &$s, $s3) {
	{
		$GLOBALS['%s']->push("hant.CmdOptions::getHelpMessage@107");
		$__hx__spos2 = $GLOBALS['%s']->length;
		{
			$tmp = _hx_string_or_null($prefix) . _hx_string_or_null(hant_CmdOptions_5($__hx__this, $_g11, $_g2, $helpLines, $maxSwitchLength, $opt1, $prefix, $s, $s3)) . _hx_string_or_null($s3) . "\x0A";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
}
function hant_CmdOptions_3(&$_g, &$arg, $r) {
	{
		$GLOBALS['%s']->push("hant.CmdOptions::parseElement@147");
		$__hx__spos2 = $GLOBALS['%s']->length;
		$_g->args->unshift($r->matched(2));
		{
			$tmp = $r->matched(1);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
}
function hant_CmdOptions_4(&$name, $opt) {
	{
		$GLOBALS['%s']->push("hant.CmdOptions::hasOption@218");
		$__hx__spos2 = $GLOBALS['%s']->length;
		{
			$tmp = $opt->name === $name;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
}
function hant_CmdOptions_5(&$__hx__this, &$_g11, &$_g2, &$helpLines, &$maxSwitchLength, &$opt1, &$prefix, &$s, &$s3) {
	{
		$l2 = $maxSwitchLength + 1;
		if(strlen(" ") === 0 || strlen("") >= $l2) {
			return "";
		} else {
			return str_pad("", Math::ceil(($l2 - strlen("")) / strlen(" ")) * strlen(" ") + strlen(""), " ", STR_PAD_LEFT);
		}
		unset($l2);
	}
}
