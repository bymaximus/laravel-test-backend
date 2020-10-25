<?php

namespace App\Containers\Geral\Helpers;

class FileSystemHelper
{
	public static function remove($path)
	{
		if (is_string($path)) {
			if (@is_file($path)) {
				return @unlink($path);
			} elseif (@is_dir($path)) {
				return array_map([
					'FileSystemHelper',
					'remove'
				], glob($path . (StringHelper::endsWith($path, '/') ? '*' : '/*'))) == @rmdir($path);
			}
		}
		return false;
	}

	public static function clear($path)
	{
		if (self::is_dir_empty($path)) {
			return true;
		}
		return (is_string($path) && @is_file($path)) ? @unlink($path) : array_map([
			'FileSystemHelper',
			'remove'
		], glob($path . (StringHelper::endsWith($path, '/') ? '*' : '/*')));
	}

	public static function is_dir_empty($dir)
	{
		if (!@is_readable($dir)) {
			return null;
		}
		if ($handle = opendir($dir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..') {
					@closedir($handle);
					return false;
				}
			}
			@closedir($handle);
			return true;
		}
		return null;
	}

	public static function fileExists($path)
	{
		return self::exists($path);
	}

	public static function exists($path)
	{
		return @file_exists($path);
	}

	public static function mkdir($pathname, $mode = 0777)
	{
		if (strpos($pathname, '/') >= 0 ||
			strpos($pathname, '\\') >= 0
		) {
			if (stripos(php_uname(), 'windows') !== false) {
				$path = '\\';
				$directories = explode('\\', $pathname);
				if ($directories) {
					foreach ($directories as $dir) {
						if (!$dir) {
							continue;
						}
						$path .= $dir . '\\';
						if (!self::isDir($path) &&
							!@mkdir($path, $mode)
						) {
							return false;
						}
						@chmod($path, $mode);
					}
				}
			} else {
				/*$path = '/';
				$directories = explode('/', $pathname);
				if ($directories) {
					foreach ($directories as $dir) {
						if (! $dir) {
							continue;
						}
						$path .= $dir . '/';
						if (! self::isDir($path) &&
							! @mkdir($path, $mode)
						) {
							return false;
						}
						@chmod($path, $mode);
					}
				}*/
			}
		}
		if (self::isDir($pathname) ||
			@mkdir($pathname, $mode, true)
		) {
			@chmod($pathname, $mode);
			return true;
		}
		return false;
	}

	public static function isDir($path)
	{
		return @is_dir($path);
	}

	public static function filewrite($path, $content, $mode = 'w', $chmod = 0777)
	{
		if ($path_info = @pathinfo($path)) {
			if (!@file_exists($path_info['dirname'])) {
				self::mkdir($path_info['dirname']);
			}
		}
		$fp = fopen($path, $mode);
		$res = fputs($fp, $content);
		fclose($fp);
		@chmod($path, $chmod);

		return $res;
	}

	public static function filewriteCSV($path, $content, $delimiter = ',', $mode = 'w', $chmod = 0777)
	{
		if ($path_info = @pathinfo($path)) {
			if (!@file_exists($path_info['dirname'])) {
				self::mkdir($path_info['dirname']);
			}
		}

		$fp = fopen($path, $mode);
		foreach ($content as $row) {
			$res = fputcsv($fp, $row, $delimiter);
		}
		fclose($fp);
		@chmod($path, $chmod);

		return $res;
	}

	public static function readDir($path, $ignore = '/^(\.svn|\.subversion|\.|\.\.)$/', $filesOnly = false, $dirOnly = false)
	{
		if (!self::isDir($path) ||
			!@file_exists($path)
		) {
			return false;
		}

		if ($handle = opendir($path)) {
			$paths = [];
			while (false !== ($file = readdir($handle))) {
				if (preg_match($ignore, $file) ||
					($filesOnly && is_dir($path . $file)) ||
					($dirOnly && !is_dir($path . $file))
				) {
					continue;
				}
				$paths[] = $file;
			}
			closedir($handle);
			return $paths;
		}
		return false;
	}

	public static function getAllFilesRecursively($path, $ignore = '/^(\.svn|\.subversion|\.|\.\.)$/')
	{
		$files = [];
		$paths = self::readDir($path, $ignore);
		if ($paths) {
			foreach ($paths as $fname) {
				if (@is_dir($path . '/' . $fname)) {
					$files = array_merge($files, self::getAllFilesRecursively($path . '/' . $fname));
				} elseif (@is_file($path . '/' . $fname)) {
					$files[] = $path . '/' . $fname;
				}
			}
		}
		return $files;
	}

	public static function readfile($path)
	{
		if (!@file_exists($path)) {
			return false;
		} else {
			return file_get_contents($path);
		}
	}

	public static function corectMBSize($size)
	{
		// less than 1 kb
		if ($size < 1024) {
			return number_format($size, 2) . ' bytes';
		}        // less than 1 mb.
		elseif ($size < 1048576) {
			return number_format($size / 1024, 2) . ' kb';
		} elseif ($size >= 1048576) {
			return number_format($size / 1048576, 2) . ' mb';
		}
	}

	public static function rename($oldPath, $newPath, $overwrite = true)
	{
		if ($overwrite) {
			self::remove($newPath);
		}
		return rename($oldPath, $newPath);
	}

	public static function copy($oldPath, $newPath, $overwrite = true)
	{
		if ($overwrite) {
			self::remove($newPath);
		}
		return copy($oldPath, $newPath);
	}

	/**
	 * Return file line count
	 * @param string $path
	 * @return number
	 */
	public static function linesCount($path)
	{
		$lines = 0;
		if (stripos(php_uname(), 'windows') !== false) {
			$handle = fopen($path, 'rb');
			while (!feof($handle)) {
				$lines += substr_count(fread($handle, 8192), "\n");
			}
			fclose($handle);
		} else {
			$lines = intval(exec("wc -l '$path'"));
		}
		return $lines;
	}
}
