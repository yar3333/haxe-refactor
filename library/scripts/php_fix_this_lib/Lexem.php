<?php
/**
 * Generated by Haxe 4.0.0-rc.2+77068e10c
 */

use \php\Boot;

class Lexem {
	/**
	 * @var string
	 */
	public $text;
	/**
	 * @var \LexemType
	 */
	public $type;

	/**
	 * @param \LexemType $type
	 * @param string $text
	 * 
	 * @return void
	 */
	public function __construct ($type, $text) {
		#src/PhpFixThis.hx:23: characters 3-19
		$this->type = $type;
		#src/PhpFixThis.hx:24: characters 3-19
		$this->text = $text;
	}
}

Boot::registerClass(Lexem::class, 'Lexem');
