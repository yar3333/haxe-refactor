<?php
/**
 * Generated by Haxe 4.0.0-rc.2+77068e10c
 */

namespace sys\io;

use \php\Boot;

/**
 * API for reading and writing to files.
 * See `sys.FileSystem` for the complementary file system API.
 */
class File {
	/**
	 * Copies the contents of the file specified by `srcPath` to the file
	 * specified by `dstPath`.
	 * If the `srcPath` does not exist or cannot be read, or if the `dstPath`
	 * file cannot be written to, an exception is thrown.
	 * If the file at `dstPath` exists, its contents are overwritten.
	 * If `srcPath` or `dstPath` are null, the result is unspecified.
	 * 
	 * @param string $srcPath
	 * @param string $dstPath
	 * 
	 * @return void
	 */
	static public function copy ($srcPath, $dstPath) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/sys/io/File.hx:67: characters 3-32
		copy($srcPath, $dstPath);
	}

	/**
	 * Retrieves the content of the file specified by `path` as a String.
	 * If the file does not exist or can not be read, an exception is thrown.
	 * `sys.FileSystem.exists` can be used to check for existence.
	 * If `path` is null, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function getContent ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/sys/io/File.hx:30: characters 3-33
		return file_get_contents($path);
	}

	/**
	 * Stores `content` in the file specified by `path`.
	 * If the file cannot be written to, an exception is thrown.
	 * If `path` or `content` are null, the result is unspecified.
	 * 
	 * @param string $path
	 * @param string $content
	 * 
	 * @return void
	 */
	static public function saveContent ($path, $content) {
		#C:\MyProg\_tools\motion-twin\haxe\std/php/_std/sys/io/File.hx:38: characters 3-35
		file_put_contents($path, $content);
	}
}

Boot::registerClass(File::class, 'sys.io.File');
