<?php

namespace App\Containers\Geral\Helpers;

class Helpers
{
	public static $helperEncoder = null;

	public static $helperString = null;

	public static $helperFileSystem = null;

	public static $helperDateTime = null;

	public static $helperEncoding = null;

	public static function encode($new = false)
	{
		if ($new ||
			!self::$helperEncoder
		) {
			self::$helperEncoder = new EncodeHelper();
		}
		return self::$helperEncoder;
	}

	public static function string($new = false)
	{
		if ($new ||
			!self::$helperString
		) {
			self::$helperString = new StringHelper();
		}
		return self::$helperString;
	}

	public static function fileSystem($new = false)
	{
		if ($new ||
			!self::$helperFileSystem
		) {
			self::$helperFileSystem = new FileSystemHelper();
		}
		return self::$helperFileSystem;
	}

	public static function dateTime($new = false)
	{
		if ($new ||
			!self::$helperDateTime
		) {
			self::$helperDateTime = new DateTimeHelper();
		}
		return self::$helperDateTime;
	}

	public static function encoding($new = false)
	{
		if ($new ||
			!self::$helperEncoding
		) {
			self::$helperEncoding = new EncodingHelper();
		}
		return self::$helperEncoding;
	}
}
