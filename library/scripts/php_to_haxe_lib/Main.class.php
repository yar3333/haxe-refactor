<?php

class Main {
	public function __construct(){}
	static function main() {
		$GLOBALS['%s']->push("Main::main");
		$__hx__spos = $GLOBALS['%s']->length;
		$args = Sys::args();
		if($args->length < 3 || $args->length > 4) {
			hant_Log::hecho("Usage: php_to_haxe [ --config <pathToConfigFile.json> ] code|extern <in_file.php> [<out_file.hx>]", null);
		} else {
			$options = new hant_CmdOptions();
			$options->add("configFile", "php_to_haxe.json", (new _hx_array(array("--config"))), "Path to config file. Default is 'php_to_haxe.json'.");
			$options->add("mode", "", null, null);
			$options->add("from", "", null, null);
			$options->add("to", "", null, null);
			$options->parse($args);
			$configFile = $options->get("configFile");
			$config = null;
			if(file_exists($configFile)) {
				$config = Main::loadConfig($configFile);
			} else {
				if(haxe_io_Path::directory($configFile) === "") {
					$config = Main::loadConfig(_hx_string_or_null(haxe_io_Path::directory(Sys::executablePath())) . "/" . _hx_string_or_null($configFile));
				} else {
					$config = null;
				}
			}
			if($config === null) {
				Sys::println("Config file '" . _hx_string_or_null($configFile) . "' not found.");
				{
					$GLOBALS['%s']->pop();
					return 1;
				}
			}
			$mode = $options->get("mode");
			$from = $options->get("from");
			$to = $options->get("to");
			if($to === "") {
				$to = haxe_io_Path::join((new _hx_array(array(haxe_io_Path::directory($from), _hx_string_or_null(stdlib_StringTools::capitalize(haxe_io_Path::withoutDirectory(haxe_io_Path::withoutExtension($from)))) . ".hx"))));
			}
			hant_Log::start(_hx_string_or_null($from) . " => " . _hx_string_or_null($to), null);
			try {
				$phpToHaxe = new PhpToHaxe($config->typeNamesMapping, $config->varNamesMapping, $config->functionNameMapping, $config->magickFunctionNameMapping, $config->reservedWords, $mode === "extern");
				if(!file_exists($from)) {
					throw new HException("Input file not exists.");
				}
				$inp = sys_io_File::getContent($from);
				$out = $phpToHaxe->getHaxeCode($inp);
				sys_io_File::saveContent($to, $out);
				hant_Log::finishSuccess(null);
			}catch(Exception $__hx__e) {
				$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
				$e = $_ex_;
				{
					$GLOBALS['%e'] = (new _hx_array(array()));
					while($GLOBALS['%s']->length >= $__hx__spos) {
						$GLOBALS['%e']->unshift($GLOBALS['%s']->pop());
					}
					$GLOBALS['%s']->push($GLOBALS['%e'][0]);
					haxe_Log::trace(stdlib_Exception::string($e), _hx_anonymous(array("fileName" => "Main.hx", "lineNumber" => 72, "className" => "Main", "methodName" => "main")));
				}
			}
		}
		{
			$GLOBALS['%s']->pop();
			return 0;
		}
		$GLOBALS['%s']->pop();
	}
	static function loadConfig($path) {
		$GLOBALS['%s']->push("Main::loadConfig");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!file_exists($path)) {
			haxe_Log::trace("Not found: " . _hx_string_or_null($path), _hx_anonymous(array("fileName" => "Main.hx", "lineNumber" => 83, "className" => "Main", "methodName" => "loadConfig")));
			{
				$GLOBALS['%s']->pop();
				return null;
			}
		}
		$r = tjson_TJSON::parse(sys_io_File::getContent($path), null, null);
		$r->typeNamesMapping = stdlib_Std::hash($r->typeNamesMapping);
		$r->varNamesMapping = stdlib_Std::hash($r->varNamesMapping);
		$r->functionNameMapping = stdlib_Std::hash($r->functionNameMapping);
		$r->magickFunctionNameMapping = stdlib_Std::hash($r->magickFunctionNameMapping);
		$r->reservedWords = $r->reservedWords;
		{
			$GLOBALS['%s']->pop();
			return $r;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Main'; }
}
