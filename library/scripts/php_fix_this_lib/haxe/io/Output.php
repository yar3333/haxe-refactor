<?php
/**
 * Generated by Haxe 4.0.0-rc.2+77068e10c
 */

namespace haxe\io;

use \php\Boot;

/**
 * An Output is an abstract write. A specific output implementation will only
 * have to override the `writeByte` and maybe the `write`, `flush` and `close`
 * methods. See `File.write` and `String.write` for two ways of creating an
 * Output.
 */
class Output {
}

Boot::registerClass(Output::class, 'haxe.io.Output');
