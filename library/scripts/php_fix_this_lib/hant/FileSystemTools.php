<?php
/**
 * Generated by Haxe 4.0.0-rc.2+77068e10c
 */

namespace hant;

use \php\Boot;
use \stdlib\Exception;
use \haxe\CallStack;
use \sys\io\Process;
use \sys\io\File;
use \haxe\io\Path as IoPath;
use \php\_Boot\HxString;
use \sys\FileSystem;
use \php\_Boot\HxException;

class FileSystemTools {
	/**
	 * @param string $src
	 * @param string $dest
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function copyFile ($src, $dest, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:234: lines 234-247
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:235: characters 3-56
		if ($verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:235: characters 16-56
			Log::start("Copy " . ($src??'null') . " => " . ($dest??'null'));
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:236: lines 236-245
		try {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:238: characters 4-48
			FileSystemTools::createDirectory(IoPath::directory($dest), false);
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:239: characters 4-29
			File::copy($src, $dest);
		} catch (\Throwable $__hx__caught_e) {
			CallStack::saveExceptionTrace($__hx__caught_e);
			$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
			$e = $__hx__real_e;
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:243: characters 4-33
			if ($verbose) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:243: characters 17-33
				Log::finishFail();
			}
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:244: characters 4-24
			Exception::rethrow($e);
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:246: characters 3-35
		if ($verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:246: characters 16-35
			Log::finishSuccess();
		}
	}

	/**
	 * @param string $src
	 * @param string $dest
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function copyFolderContent ($src, $dest, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:79: lines 79-91
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:80: characters 3-28
		$src = Path::normalize($src);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:81: characters 9-36
		$dest = Path::normalize($dest);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:83: characters 3-75
		if ($verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:83: characters 16-75
			Log::start("Copy directory '" . ($src??'null') . "' => '" . ($dest??'null') . "'");
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:85: lines 85-88
		FileSystemTools::findFiles($src, function ($path)  use (&$dest, &$src, &$verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:87: characters 26-49
			$tmp = mb_substr($path, mb_strlen($src), null);
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:87: characters 4-59
			FileSystemTools::copyFile($path, ($dest??'null') . ($tmp??'null'), $verbose);
		});
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:90: characters 3-35
		if ($verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:90: characters 16-35
			Log::finishSuccess();
		}
	}

	/**
	 * @param string $path
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function createDirectory ($path, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:59: lines 59-76
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:60: characters 3-30
		$path = Path::normalize($path);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:62: characters 13-51
		$tmp = null;
		if ($path !== "") {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:62: characters 28-51
			clearstatcache(true, $path);
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:62: characters 13-51
			$tmp = !file_exists($path);
		} else {
			$tmp = false;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:62: lines 62-75
		if ($tmp) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:64: characters 4-61
			if ($verbose) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:64: characters 17-61
				Log::start("Create directory '" . ($path??'null') . "'");
			}
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:65: lines 65-74
			try {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:67: characters 5-37
				FileSystem::createDirectory($path);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:68: characters 5-37
				if ($verbose) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:68: characters 18-37
					Log::finishSuccess();
				}
			} catch (\Throwable $__hx__caught_e) {
				CallStack::saveExceptionTrace($__hx__caught_e);
				$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
				if (is_string($__hx__real_e)) {
					$message = $__hx__real_e;
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:72: characters 5-41
					if ($verbose) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:72: characters 18-41
						Log::finishFail($message);
					}
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:73: characters 5-31
					Exception::rethrow($message);
				} else  throw $__hx__caught_e;
			}
		}
	}

	/**
	 * @param string $path
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function deleteAny ($path, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:182: lines 182-192
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:182: characters 7-30
		clearstatcache(true, $path);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:182: lines 182-192
		if (file_exists($path)) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:184: lines 184-191
			if (FileSystem::isDirectory($path)) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:186: characters 5-35
				FileSystemTools::deleteDirectory($path, $verbose);
			} else {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:190: characters 5-30
				FileSystemTools::deleteFile($path, $verbose);
			}
		}
	}

	/**
	 * @param string $path
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function deleteDirectory ($path, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:134: lines 134-159
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:134: characters 13-36
		clearstatcache(true, $path);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:134: lines 134-159
		if (file_exists($path)) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:136: characters 4-61
			if ($verbose) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:136: characters 17-61
				Log::start("Delete directory '" . ($path??'null') . "'");
			}
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:137: lines 137-158
			try {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:139: lines 139-149
				$_g = 0;
				$_g1 = FileSystem::readDirectory($path);
				while ($_g < $_g1->length) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:139: characters 10-14
					$file = ($_g1->arr[$_g] ?? null);
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:139: lines 139-149
					++$_g;
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:141: lines 141-148
					if (FileSystem::isDirectory(($path??'null') . "/" . ($file??'null'))) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:143: characters 7-50
						FileSystemTools::deleteDirectory(($path??'null') . "/" . ($file??'null'), $verbose);
					} else {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:147: characters 7-45
						FileSystemTools::deleteFile(($path??'null') . "/" . ($file??'null'), $verbose);
					}
				}

				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:151: characters 5-37
				rmdir($path);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:152: characters 5-37
				if ($verbose) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:152: characters 18-37
					Log::finishSuccess();
				}
			} catch (\Throwable $__hx__caught_e) {
				CallStack::saveExceptionTrace($__hx__caught_e);
				$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
				if (is_string($__hx__real_e)) {
					$message = $__hx__real_e;
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:156: characters 5-41
					if ($verbose) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:156: characters 18-41
						Log::finishFail($message);
					}
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:157: characters 5-31
					Exception::rethrow($message);
				} else  throw $__hx__caught_e;
			}
		}
	}

	/**
	 * @param string $path
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function deleteFile ($path, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:164: lines 164-177
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:164: characters 13-36
		clearstatcache(true, $path);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:164: lines 164-177
		if (file_exists($path)) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:166: characters 4-56
			if ($verbose) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:166: characters 17-56
				Log::start("Delete file '" . ($path??'null') . "'");
			}
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:167: lines 167-176
			try {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:169: characters 5-32
				unlink($path);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:170: characters 5-37
				if ($verbose) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:170: characters 18-37
					Log::finishSuccess();
				}
			} catch (\Throwable $__hx__caught_e) {
				CallStack::saveExceptionTrace($__hx__caught_e);
				$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
				if (is_string($__hx__real_e)) {
					$message = $__hx__real_e;
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:174: characters 5-41
					if ($verbose) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:174: characters 18-41
						Log::finishFail($message);
					}
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:175: characters 5-31
					Exception::rethrow($message);
				} else  throw $__hx__caught_e;
			}
		}
	}

	/**
	 * @param string $path
	 * @param \Closure $onFile
	 * @param \Closure $onDir
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function findFiles ($path, $onFile = null, $onDir = null, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:18: lines 18-55
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:18: characters 7-30
		clearstatcache(true, $path);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:18: lines 18-55
		if (file_exists($path)) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:20: lines 20-54
			if (FileSystem::isDirectory($path)) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:22: lines 22-49
				$_g = 0;
				$_g1 = FileSystem::readDirectory($path);
				while ($_g < $_g1->length) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:22: characters 10-14
					$file = ($_g1->arr[$_g] ?? null);
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:22: lines 22-49
					++$_g;
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:24: characters 6-30
					$isDir = null;
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:25: lines 25-32
					try {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:27: characters 7-56
						$isDir = FileSystem::isDirectory(($path??'null') . "/" . ($file??'null'));
					} catch (\Throwable $__hx__caught_e) {
						CallStack::saveExceptionTrace($__hx__caught_e);
						$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
						$e = $__hx__real_e;
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:31: characters 7-90
						if ($verbose) {
							#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:31: characters 20-90
							Log::echo("ERROR: FileSystem.isDirectory('" . ($path??'null') . "/" . ($file??'null') . "')");
						}
					}
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:34: lines 34-48
					if ($isDir === true) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:36: lines 36-42
						if (($file !== ".svn") && ($file !== ".hg") && ($file !== ".git")) {
							#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:38: lines 38-41
							if (($onDir === null) || $onDir(($path??'null') . "/" . ($file??'null'))) {
								#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:40: characters 9-61
								FileSystemTools::findFiles(($path??'null') . "/" . ($file??'null'), $onFile, $onDir, $verbose);
							}
						}
					} else if ($isDir === false) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:47: characters 7-52
						if ($onFile !== null) {
							#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:47: characters 27-52
							$onFile(($path??'null') . "/" . ($file??'null'));
						}
					}
				}
			} else if ($onFile !== null) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:53: characters 25-37
				$onFile($path);
			}
		}
	}

	/**
	 * @param string $path
	 * 
	 * @return bool
	 */
	static public function getHiddenFileAttribute ($path) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:212: lines 212-221
		if (\Sys::systemName() === "Windows") {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:214: characters 4-44
			$p = new Process("attrib", \Array_hx::wrap([$path]));
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:215: characters 4-42
			$s = $p->stdout->readAll()->toString();
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:216: lines 216-220
			if (mb_strlen($s) > 12) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:218: characters 5-24
				$s = mb_substr($s, 0, 12);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:219: characters 5-31
				return HxString::indexOf($s, "H") >= 0;
			}
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:222: characters 3-15
		return false;
	}

	/**
	 * @param string $pathA
	 * @param string $pathB
	 * 
	 * @return bool
	 */
	static public function isSamePaths ($pathA, $pathB) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:324: characters 3-37
		$pathA = (realpath($pathA) ?: null);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:325: characters 3-37
		$pathB = (realpath($pathB) ?: null);
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:326: characters 10-103
		if ($pathA !== $pathB) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:326: characters 28-103
			if (mb_strtolower($pathA) === mb_strtolower($pathB)) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:326: characters 74-103
				return \Sys::systemName() === "Windows";
			} else {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:326: characters 28-103
				return false;
			}
		} else {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:326: characters 10-103
			return true;
		}
	}

	/**
	 * @param string $src
	 * @param string $dest
	 * 
	 * @return void
	 */
	static public function nativeCopyFile ($src, $dest) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:318: characters 3-23
		File::copy($src, $dest);
	}

	/**
	 * Find and remove empty directories. Return true if specified base directory was removed.
	 * 
	 * @param string $baseDir
	 * @param bool $removeSelf
	 * 
	 * @return bool
	 */
	static public function removeEmptyDirectories ($baseDir, $removeSelf = false) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:253: lines 253-277
		if ($removeSelf === null) {
			$removeSelf = false;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:254: characters 3-22
		$childCount = 0;
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:256: lines 256-268
		$_g = 0;
		$_g1 = FileSystem::readDirectory($baseDir);
		while ($_g < $_g1->length) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:256: characters 8-12
			$file = ($_g1->arr[$_g] ?? null);
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:256: lines 256-268
			++$_g;
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:258: characters 4-16
			++$childCount;
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:260: characters 4-22
			$isDir = false;
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:261: lines 261-262
			try {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:261: characters 8-60
				$isDir = FileSystem::isDirectory(($baseDir??'null') . "/" . ($file??'null'));
			} catch (\Throwable $__hx__caught_e) {
				CallStack::saveExceptionTrace($__hx__caught_e);
				$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
				$e = $__hx__real_e;
							}
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:264: lines 264-267
			if ($isDir) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:266: characters 5-73
				if (FileSystemTools::removeEmptyDirectories(($baseDir??'null') . "/" . ($file??'null'), true)) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:266: characters 61-73
					--$childCount;
				}
			}
		}

		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:270: lines 270-274
		if ($removeSelf && ($childCount === 0)) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:272: characters 4-39
			rmdir($baseDir);
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:273: characters 4-15
			return true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:276: characters 3-15
		return false;
	}

	/**
	 * @param string $path
	 * @param string $newpath
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function rename ($path, $newpath, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:94: lines 94-130
		if ($verbose === null) {
			$verbose = true;
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:95: characters 9-77
		if ($verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:95: characters 22-77
			Log::start("Rename '" . ($path??'null') . "' => '" . ($newpath??'null') . "'");
		}
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:96: lines 96-129
		try {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:98: characters 17-40
			clearstatcache(true, $path);
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:98: lines 98-122
			if (file_exists($path)) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:100: characters 5-52
				FileSystemTools::createDirectory(IoPath::directory($newpath), false);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:102: lines 102-117
				if (!FileSystem::isDirectory($path)) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:104: characters 10-36
					clearstatcache(true, $newpath);
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:104: lines 104-107
					if (file_exists($newpath) && !FileSystemTools::isSamePaths($path, $newpath)) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:106: characters 7-37
						unlink($newpath);
					}
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:108: characters 6-38
					rename($path, $newpath);
				} else {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:112: characters 10-36
					clearstatcache(true, $newpath);
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:112: lines 112-115
					if (file_exists($newpath) && !FileSystemTools::isSamePaths($path, $newpath)) {
						#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:114: characters 7-42
						rmdir($newpath);
					}
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:116: characters 6-38
					rename($path, $newpath);
				}
			} else {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:121: characters 17-22
				throw new HxException("File '" . ($path??'null') . "' not found.");
			}
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:123: characters 13-45
			if ($verbose) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:123: characters 26-45
				Log::finishSuccess();
			}
		} catch (\Throwable $__hx__caught_e) {
			CallStack::saveExceptionTrace($__hx__caught_e);
			$__hx__real_e = ($__hx__caught_e instanceof HxException ? $__hx__caught_e->e : $__hx__caught_e);
			if (is_string($__hx__real_e)) {
				$message = $__hx__real_e;
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:127: characters 4-40
				if ($verbose) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:127: characters 17-40
					Log::finishFail($message);
				}
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:128: characters 4-30
				Exception::rethrow($message);
			} else  throw $__hx__caught_e;
		}
	}

	/**
	 * @param string $src
	 * @param string $dest
	 * @param \EReg $filter
	 * @param bool $verbose
	 * 
	 * @return void
	 */
	static public function restoreFileTimes ($src, $dest, $filter = null, $verbose = true) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:197: lines 197-207
		if ($verbose === null) {
			$verbose = true;
		}
		FileSystemTools::findFiles($src, function ($srcFile)  use (&$filter, &$dest, &$src, &$verbose) {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:199: lines 199-206
			if (($filter === null) || $filter->match($srcFile)) {
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:201: characters 27-53
				$destFile = mb_substr($srcFile, mb_strlen($src), null);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:201: characters 5-54
				$destFile1 = ($dest??'null') . ($destFile??'null');
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:202: characters 9-36
				clearstatcache(true, $destFile1);
				#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:202: lines 202-205
				if (file_exists($destFile1) && (File::getContent($srcFile) === File::getContent($destFile1))) {
					#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:204: characters 6-40
					FileSystemTools::rename($srcFile, $destFile1, $verbose);
				}
			}
		}, null, $verbose);
	}

	/**
	 * @param string $path
	 * @param bool $hidden
	 * 
	 * @return void
	 */
	static public function setHiddenFileAttribute ($path, $hidden) {
		#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:227: lines 227-230
		if (\Sys::systemName() === "Windows") {
			#c:\MyProg\_haxelibs\hant\library\hant/FileSystemTools.hx:229: characters 4-63
			\Sys::command("attrib", \Array_hx::wrap([
				((($hidden ? "+" : "-"))??'null') . "H",
				$path,
			]));
		}
	}
}

Boot::registerClass(FileSystemTools::class, 'hant.FileSystemTools');
