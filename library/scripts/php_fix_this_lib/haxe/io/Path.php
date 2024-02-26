<?php
/**
 * Generated by Haxe 4.0.0-rc.2+77068e10c
 */

namespace haxe\io;

use \php\Boot;
use \php\_Boot\HxString;

/**
 * This class provides a convenient way of working with paths. It supports the
 * common path formats:
 * - `directory1/directory2/filename.extension`
 * - `directory1\directory2\filename.extension`
 */
class Path {
	/**
	 * @var bool
	 * `true` if the last directory separator is a backslash, `false` otherwise.
	 */
	public $backslash;
	/**
	 * @var string
	 * The directory.
	 * This is the leading part of the path that is not part of the file name
	 * and the extension.
	 * Does not end with a `/` or `\` separator.
	 * If the path has no directory, the value is `null`.
	 */
	public $dir;
	/**
	 * @var string
	 * The file extension.
	 * It is separated from the file name by a dot. This dot is not part of
	 * the extension.
	 * If the path has no extension, the value is `null`.
	 */
	public $ext;
	/**
	 * @var string
	 * The file name.
	 * This is the part of the part between the directory and the extension.
	 * If there is no file name, e.g. for `".htaccess"` or `"/dir/"`, the value
	 * is the empty String `""`.
	 */
	public $file;

	/**
	 * Adds a trailing slash to `path`, if it does not have one already.
	 * If the last slash in `path` is a backslash, a backslash is appended to
	 * `path`.
	 * If the last slash in `path` is a slash, or if no slash is found, a slash
	 * is appended to `path`. In particular, this applies to the empty String
	 * `""`.
	 * If `path` is `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function addTrailingSlash ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:265: lines 265-266
		if (mb_strlen($path) === 0) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:266: characters 4-14
			return "/";
		}
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:267: characters 3-34
		$c1 = HxString::lastIndexOf($path, "/");
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:268: characters 3-35
		$c2 = HxString::lastIndexOf($path, "\\");
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:269: lines 269-275
		if ($c1 < $c2) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:270: lines 270-271
			if ($c2 !== (mb_strlen($path) - 1)) {
				#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:270: characters 31-42
				return ($path??'null') . "\\";
			} else {
				#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:271: characters 9-13
				return $path;
			}
		} else if ($c1 !== (mb_strlen($path) - 1)) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:273: characters 31-41
			return ($path??'null') . "/";
		} else {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:274: characters 9-13
			return $path;
		}
	}

	/**
	 * Returns the directory of `path`.
	 * If the directory is `null`, the empty String `""` is returned.
	 * If `path` is `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function directory ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:148: characters 3-26
		$s = new Path($path);
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:149: lines 149-150
		if ($s->dir === null) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:150: characters 4-13
			return "";
		}
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:151: characters 3-15
		return $s->dir;
	}

	/**
	 * Returns the extension of `path`.
	 * If `path` has no extension, the empty String `""` is returned.
	 * If `path` is `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function extension ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:162: characters 3-26
		$s = new Path($path);
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:163: lines 163-164
		if ($s->ext === null) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:164: characters 4-13
			return "";
		}
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:165: characters 3-15
		return $s->ext;
	}

	/**
	 * Removes trailing slashes from `path`.
	 * If `path` does not end with a `/` or `\`, `path` is returned unchanged.
	 * Otherwise the substring of `path` excluding the trailing slashes or
	 * backslashes is returned.
	 * If `path` is `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function removeTrailingSlashes ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:290: lines 290-295
		while (true) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:291: characters 11-43
			$_g = HxString::charCodeAt($path, mb_strlen($path) - 1);
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:291: lines 291-293
			if ($_g === null) {
				#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:293: characters 13-18
				break;
			} else {
				#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:291: characters 11-43
				if ($_g === 47 || $_g === 92) {
					#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:292: characters 32-57
					$path = mb_substr($path, 0, -1);
				} else {
					#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:293: characters 13-18
					break;
				}
			}
		};
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:296: characters 3-14
		return $path;
	}

	/**
	 * Returns a String representation of `path` where the extension is `ext`.
	 * If `path` has no extension, `ext` is added as extension.
	 * If `path` or `ext` are `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * @param string $ext
	 * 
	 * @return string
	 */
	static public function withExtension ($path, $ext) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:176: characters 3-26
		$s = new Path($path);
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:177: characters 3-14
		$s->ext = $ext;
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:178: characters 3-22
		return $s->toString();
	}

	/**
	 * Returns the String representation of `path` without the directory.
	 * If `path` is `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function withoutDirectory ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:135: characters 3-26
		$s = new Path($path);
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:136: characters 3-15
		$s->dir = null;
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:137: characters 3-22
		return $s->toString();
	}

	/**
	 * Returns the String representation of `path` without the file extension.
	 * If `path` is `null`, the result is unspecified.
	 * 
	 * @param string $path
	 * 
	 * @return string
	 */
	static public function withoutExtension ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:124: characters 3-26
		$s = new Path($path);
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:125: characters 3-15
		$s->ext = null;
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:126: characters 3-22
		return $s->toString();
	}

	/**
	 * Creates a new `Path` instance by parsing `path`.
	 * Path information can be retrieved by accessing the `dir`, `file` and `ext`
	 * properties.
	 * 
	 * @param string $path
	 * 
	 * @return void
	 */
	public function __construct ($path) {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:77: lines 77-82
		if ($path === "." || $path === "..") {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:79: characters 5-15
			$this->dir = $path;
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:80: characters 5-14
			$this->file = "";
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:81: characters 5-11
			return;
		}
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:83: characters 3-34
		$c1 = HxString::lastIndexOf($path, "/");
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:84: characters 3-35
		$c2 = HxString::lastIndexOf($path, "\\");
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:85: lines 85-93
		if ($c1 < $c2) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:86: characters 4-27
			$this->dir = mb_substr($path, 0, $c2);
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:87: characters 4-28
			$path = mb_substr($path, $c2 + 1, null);
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:88: characters 4-20
			$this->backslash = true;
		} else if ($c2 < $c1) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:90: characters 4-27
			$this->dir = mb_substr($path, 0, $c1);
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:91: characters 4-28
			$path = mb_substr($path, $c1 + 1, null);
		} else {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:93: characters 4-14
			$this->dir = null;
		}
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:94: characters 3-34
		$cp = HxString::lastIndexOf($path, ".");
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:95: lines 95-101
		if ($cp !== -1) {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:96: characters 4-27
			$this->ext = mb_substr($path, $cp + 1, null);
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:97: characters 4-28
			$this->file = mb_substr($path, 0, $cp);
		} else {
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:99: characters 4-14
			$this->ext = null;
			#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:100: characters 4-15
			$this->file = $path;
		}
	}

	/**
	 * Returns a String representation of `this` path.
	 * If `this.backslash` is `true`, backslash is used as directory separator,
	 * otherwise slash is used. This only affects the separator between
	 * `this.dir` and `this.file`.
	 * If `this.directory` or `this.extension` is `null`, their representation
	 * is the empty String `""`.
	 * 
	 * @return string
	 */
	public function toString () {
		#C:\MyProg\_tools\motion-twin\haxe\std/haxe/io/Path.hx:115: characters 3-120
		return ((($this->dir === null ? "" : ($this->dir??'null') . ((($this->backslash ? "\\" : "/"))??'null')))??'null') . ($this->file??'null') . ((($this->ext === null ? "" : "." . ($this->ext??'null')))??'null');
	}

	public function __toString() {
		return $this->toString();
	}
}

Boot::registerClass(Path::class, 'haxe.io.Path');