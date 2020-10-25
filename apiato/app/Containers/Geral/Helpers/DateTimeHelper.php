<?php

namespace App\Containers\Geral\Helpers;

use Zend\Stdlib\DateTime;

if (!defined('STRFTIME_SQLFORMAT')) {
	define('STRFTIME_SQLFORMAT', '%Y-%m-%d %H:%M:%S'); // for strftime
}
if (!defined('DATETIME_PHPFORMAT')) {
	define('DATETIME_PHPFORMAT', 'Y-m-d H:i:s');
}
if (!defined('DATE_PHPFORMAT')) {
	define('DATE_PHPFORMAT', 'Y-m-d');
}
if (!defined('STRFTIME_FORMAT')) {
	define('STRFTIME_FORMAT', '%m/%d/%Y %I:%M:%S %p'); // for strftime
}
if (!defined('STRFTIME_SHORT_SQLFORMAT')) {
	define('STRFTIME_SHORT_SQLFORMAT', '%Y-%m-%d'); // for strftime
}
if (!defined('DEFAULT_SERVER_TIMEZONE_OFFSET')) {
	define('DEFAULT_SERVER_TIMEZONE_OFFSET', '0');
}

class DateTimeHelper
{
	private static $timeZones = [
		'-12' => '(GMT-12:00) International dateline, west',
		'-11' => '(GMT-11:00) Midway Islands, Samoan Islands',
		'-10' => '(GMT-10:00) Hawaii Standard Time',
		'-9' => '(GMT-09:00) Alaska Standard Time',
		'-8' => '(GMT-08:00) Pacific Time (USA and Canada); Tijuana',
		'-7' => '(GMT-07:00) Mountain Time (USA and Canada)',
		'-6' => '(GMT-06:00) Central time (USA and Canada)',
		'-5' => '(GMT-05:00) Eastern time (USA and Canada)',
		'-4' => '(GMT-04:00) Atlantic Time (Canada)',
		'-3.5' => '(GMT-03:30) Canada Newfoundland Time',
		'-3' => '(GMT-03:00) Argentina Standard Time',
		'-2' => '(GMT-02:00) Mid-Atlantic',
		'-1' => '(GMT-01:00) Central African Time',
		'0' => '(GMT) Greenwich Mean Time: Dublin, Edinburgh, Lissabon, London',
		'+1' => '(GMT+01:00) Amsterdam, Berlin, Bern, Rom, Stockholm, Wien',
		'+2' => '(GMT+02:00) Athen, Istanbul, Minsk',
		'+3' => '(GMT+03:00) Moscow, St. Petersburg, Volgograd',
		'+3.5' => '(GMT+03:30) Teheran',
		'+4' => '(GMT+04:00) Abu Dhabi, Muscat',
		'+4.5' => '(GMT+04:30) Kabul',
		'+5' => '(GMT+05:00) Islamabad, Karachi, Tasjkent',
		'+5.5' => '(GMT+05:30) Kolkata, Chennai, Mumbai, New Delhi',
		'+5.45' => '(GMT+05:45) Katmandu',
		'+6' => '(GMT+06:00) Astana, Dhaka',
		'+6.5' => '(GMT+06:30) Rangoon',
		'+7' => '(GMT+07:00) Bangkok, Hanoi, Djakarta',
		'+8' => '(GMT+08:00) Beijing, Chongjin, SAR Hongkong',
		'+9' => '(GMT+09:00) Osaka, Sapporo, Tokyo',
		'+9.5' => '(GMT+09:30) Adelaide',
		'+10' => '(GMT+10:00) Canberra, Melbourne, Sydney',
		'+11' => '(GMT+11:00) Magadan, Solomon Islands, New Caledonien',
		'+12' => '(GMT+12:00) Fiji, Kamtjatka, Marshall Islands',
		'+13' => "(GMT+13:00) Nuku'alofa"
	];

	public static $phpToMysqlFormat = [
		'D' => '%a',
		'M' => '%b',
		'n' => '%c',
		'jS' => '%D',
		'd' => '%d',
		'j' => '%e',
		'u' => '%f',
		'H' => '%H',
		'h' => '%h',
		'i' => '%i',
		'z' => '%j',
		'G' => '%k',
		'g' => '%l',
		'F' => '%M',
		'm' => '%m',
		'A' => '%p',
		's' => '%s',
		'W' => '%v',
		'l' => '%W',
		'w' => '%w',
		'Y' => '%Y',
		'y' => '%y',
	];

	public static $phpDayTextualRelated = [
		'D', 'l', 'N', 'w', 'z',
	];

	public static $phpDayRelated = [
		'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z',
	];

	public static $phpWeekRelated = [
		'W',
	];

	public static $phpMonthRelated = [
		'F', 'm', 'M', 'n', 't',
	];

	public static $phpYearRelated = [
		'L', 'o', 'Y', 'y',
	];

	public static $phpTimeRelated = [
		'a', 'A', 'B',
	];

	public static $phpTimeHourRelated = [
		'g', 'G', 'h', 'H',
	];

	public static $phpTimeMinuteRelated = [
		'i',
	];

	public static $phpTimeSecondRelated = [
		's', 'u',
	];

	public static $phpTimezoneRelated = [
		'e', 'I', 'O', 'P', 'T', 'Z',
	];

	public static $phpDateTimeRelated = [
		'c', 'r', 'U',
	];

	public static function getTimeZoneList()
	{
		return self::$timeZones;
	}

	/**
	 * Convert a php date format to mysql format
	 * @param string $format
	 * @return string
	 */
	public static function toMysqlFormat($format)
	{
		$mysqlFormat = '';
		for ($i = 0; $i < strlen($format); $i++) {
			if ($format[$i] == 'j' &&
				($i + 1) < strlen($format) &&
				$format[$i + 1] == 'S' &&
				isset(self::$phpToMysqlFormat['jS'])
			) {
				$mysqlFormat .= self::$phpToMysqlFormat['jS'];
				$i++;
			} elseif (isset(self::$phpToMysqlFormat[$format[$i]])) {
				$mysqlFormat .= self::$phpToMysqlFormat[$format[$i]];
			} else {
				$mysqlFormat .= $format[$i];
			}
		}
		return $mysqlFormat;
	}

	/**
	 * Add or sub a dateTime
	 * according with timezone
	 *
	 * @param string $dateTime
	 * @param string $timezone
	 * @param string $format
	 */
	public static function changeDateTime($dateTime, $timezone, $format = 'Y-m-d H:i:s')
	{
		$dateTime = new \DateTime($dateTime);
		$timezone = str_replace(':', '.', $timezone);

		$hour = $timezone;
		$minute = 0;
		$minus = '';
		if (strpos($timezone, '-') > -1) {
			$minus = '-';
		}

		if (strpos($timezone, '.') > -1) {
			$temp = explode('.', $timezone);
			$hour = $temp[0];
			if ($temp[1] == 5) {
				$minute = $minus . '30';
			} else {
				$minute = $minus . $temp[1];
			}
		}
		$dateTime->modify($hour . ' HOUR ' . $minute . ' MINUTE');
		return $dateTime->format($format);
	}

	/**
	 * get gmt datetime.
	 * based on DEFAULT_SERVER_TIMEZONE_OFFSET constant
	 * default format based on STRFTIME_SQLFORMAT
	 * constants above should be defined
	 *
	 * @param $format
	 * @return string
	 */
	public static function getGMTDateTime($format = STRFTIME_SQLFORMAT)
	{
		$result = gmstrftime($format);
		return $result;
	}

	public static function getFormatedTime($dateTime, $format = STRFTIME_FORMAT)
	{
		return strftime($format, $dateTime);
	}

	public static function getTimezoneDateTime($currentTimeZone = 0, $igmDateTime = false, $format = '')
	{
		if (!$igmDateTime) {
			$gmDateTime = self::getGMTDateTime();

			$igmDateTime = strtotime($gmDateTime);
		}
		$timeDiff = strtotime((3600 * $currentTimeZone) . ' seconds', $igmDateTime);
		$format = $format ? $format : STRFTIME_SQLFORMAT;
		$result = strftime($format, $timeDiff);
		return $result;
	}

	public static function getMicrotime($precision = 2)
	{
		return round(microtime(true), $precision);
	}

	public static function changeTimezone($dateTime, $timeZoneFrom = null, $timeZoneTo = 'GMT')
	{
		if (!$timeZoneFrom) {
			$timeZoneFrom = self::getDefaultTimezone();
		}
		$buffer = date_default_timezone_get();
		date_default_timezone_set($timeZoneFrom);
		// Create a server datetime object
		$DateTime = new \DateTime($dateTime);

		$DateTime->setTimezone(new \DateTimeZone($timeZoneTo));

		date_default_timezone_set($buffer);

		return $DateTime->format('Y-m-d H:i:s');
	}

	public static function applyTimezone($dateTime, $timeZoneTo = 'GMT')
	{
		try {
			// Create a server datetime object
			$DateTime = new \DateTime($dateTime);
			$DateTime->setTimezone(new \DateTimeZone($timeZoneTo));
			return $DateTime->format('Y-m-d H:i:s');
		} catch (\Exception $e) {
			$dateTime = date('Y-m-d H:i:s T', strtotime($dateTime));
			// Create a server datetime object
			$DateTime = new \DateTime($dateTime);
			$DateTime->setTimezone(new \DateTimeZone($timeZoneTo));
			return $DateTime->format('Y-m-d H:i:s');
		}
	}

	public static function getDefaultTimezone()
	{
		return date_default_timezone_get();
	}

	public static function getDateTime($format = 'Y-m-d H:i:s')
	{
		$DateTime = new \DateTime();
		return $DateTime->format($format);
	}

	public static function formatTimeLengthShort($timelength, $show_parts = 1, $options = [])
	{
		$defaults = collect([
			'hours' => ' hours',
			'minutes' => ' mins',
			'seconds' => ' secs',
			'years' => ' years',
			'months' => ' months',
			'days' => ' days',
			'cap' => [
				'minute' => 60,
				'hour' => 3600,
				'day' => 86400,
				'month' => 2678400,
				'year' => 32140800
			]
		]);

		$options = $defaults->merge($options);
		$options = $options->all();

		return self::formatTimeLength($timelength, $options['hours'], $options['minutes'], $options['seconds'], $options['years'], $options['months'], $options['days'], $show_parts, $options['cap']);
	}

	public static function formatTimeLength(
		$timelength,
		$str_hours = ' hours',
		$str_minutes = ' mins',
		$str_seconds = ' secs',
		$str_years = ' years',
		$str_months = ' months',
		$str_days = ' days',
		$show_parts = 6,
		$cap = [
			'minute' => 60,
			'hour' => 3600,
			'day' => 86400,
			'month' => 2678400,
			'year' => 32140800
		]
	) {
		$result = [];
		$aParts = [];
		if ($timelength <= $cap['minute']) {
			// less than minute
			$aParts[] = [
				intval($timelength),
				$str_seconds
			];
		} elseif ($timelength > $cap['minute'] && $timelength < $cap['hour']) {
			// more than minute

			$minutes = floor($timelength / $cap['minute']);
			$seconds = floor(($timelength - $minutes * $cap['minute']));

			$aParts[] = [
				intval($minutes),
				$str_minutes
			];
			$aParts[] = [
				intval($seconds),
				$str_seconds
			];
		} elseif ($timelength >= $cap['hour'] && $timelength < $cap['day']) {
			// more than hour
			$hours = floor($timelength / $cap['hour']);
			$minutes = floor(($timelength - $hours * $cap['hour']) / $cap['minute']);
			$seconds = floor(($timelength - ($hours * $cap['hour'] + $minutes * $cap['minute'])));

			$aParts[] = [
				intval($hours),
				$str_hours
			];
			$aParts[] = [
				intval($minutes),
				$str_minutes
			];
			$aParts[] = [
				intval($seconds),
				$str_seconds
			];
		} elseif ($timelength >= $cap['day'] && $timelength < $cap['month']) {
			// more than day
			$days = floor($timelength / $cap['day']);
			$hours = floor(($timelength - $days * $cap['day']) / $cap['hour']);
			$minutes = floor(($timelength - $days * $cap['day'] - $hours * $cap['hour']) / $cap['minute']);
			$seconds = floor(($timelength - $days * $cap['day'] - $hours * $cap['hour'] - $minutes * $cap['minute']) / $cap['minute']);

			$aParts[] = [
				intval($days),
				$str_days
			];
			$aParts[] = [
				intval($hours),
				$str_hours
			];
			$aParts[] = [
				intval($minutes),
				$str_minutes
			];
			$aParts[] = [
				intval($seconds),
				$str_seconds
			];
		} elseif ($timelength >= $cap['month'] && $timelength < $cap['year']) {
			// more than month
			$months = floor($timelength / $cap['month']);
			$days = floor(($timelength - $months * $cap['month']) / $cap['day']);
			$hours = floor($timelength - $months * $cap['month'] - $days * $cap['day']) / $cap['hour'];
			$minutes = floor(($timelength - $months * $cap['month'] - $days * $cap['day'] - $hours * $cap['hour']) / $cap['minute']);
			$seconds = floor(($timelength - ($hours * $cap['hour'] + $minutes * $cap['minute'])));

			$aParts[] = [
				intval($months),
				$str_months
			];
			$aParts[] = [
				intval($days),
				$str_days
			];
			$aParts[] = [
				intval($hours),
				$str_hours
			];
			$aParts[] = [
				intval($minutes),
				$str_minutes
			];
			$aParts[] = [
				intval($seconds),
				$str_seconds
			];
		} elseif ($timelength >= $cap['year']) {
			// more than year
			$years = floor($timelength / $cap['year']);
			$months = floor(($timelength - $years * $cap['year']) / $cap['month']);
			$days = floor(($timelength - $years * $cap['year'] - $months * $cap['month']) / $cap['day']);
			$hours = floor(($timelength - $years * $cap['year'] - $months * $cap['month'] - $days * $cap['day']) / $cap['hour']);
			$minutes = floor(($timelength - $years * $cap['year'] - $months * $cap['month'] - $days * $cap['day'] - $hours * $cap['hour']) / $cap['minute']);
			$seconds = floor(($timelength - $years * $cap['year'] - $months * $cap['month'] - $days * $cap['day'] - $hours * $cap['hour'] - $minutes * $cap['minute']));

			$aParts[] = [
				intval($years),
				$str_years
			];
			$aParts[] = [
				intval($months),
				$str_months
			];
			$aParts[] = [
				intval($days),
				$str_days
			];
			$aParts[] = [
				intval($hours),
				$str_hours
			];
			$aParts[] = [
				intval($minutes),
				$str_minutes
			];
			$aParts[] = [
				intval($seconds),
				$str_seconds
			];
		}
		$i_max = count($aParts);
		if ($show_parts < $i_max) {
			$i_max = $show_parts;
		}
		for ($i = 0; $i < $i_max; $i++) {
			$result[] = $aParts[$i];
		}
		return $result;
	}

	/**
	 * Calculate difference between $dateFrom and $dateTo
	 *
	 * @param string $dateFrom
	 * @param string $dateTo
	 * @return boolean (return false if $dateFrom is superior than $dateTo)
	 */
	public static function getDiff($dateFrom, $dateTo = 'now')
	{
		$dateFrom = new \DateTime(date('d-M-Y', strtotime($dateFrom)));
		if ($dateTo == 'now') {
			$dateTo = new \DateTime(date('d-M-Y'));
		} else {
			$dateTo = new \DateTime(date('d-M-Y', strtotime($dateTo)));
		}
		if ($dateFrom > $dateTo) {
			return false;
		}
		return date_diff($dateFrom, $dateTo);
	}

	public static function getFormatedDateTime($dateTime, $format = STRFTIME_FORMAT)
	{
		$date = date_create($dateTime);
		return date_format($date, $format);
	}

	/**
	 * Get all dates between start and end
	 * @param string $dateStart
	 * @param string $dateEnd
	 * @param string $format
	 * @param string $period
	 */
	public static function getDatesByPeriod(
		$dateStart,
		$dateEnd,
		$format = 'Y-m-d',
		$period = 'P1D'
	) {
		$period = new \DatePeriod(new \DateTime($dateStart), new \DateInterval($period), new \DateTime($dateEnd));

		$dates = [];
		foreach ($period as $date) {
			$dates[] = $date->format($format);
		}
		if (end($dates) != date($format, strtotime($dateEnd)) &&
			!in_array(date($format, strtotime($dateEnd)), $dates)
		) {
			$dates[] = date($format, strtotime($dateEnd));
		}
		return $dates;
	}

	/**
	 * Filter datetime format
	 * @param string $format
	 * @param array $excludedFormat
	 */
	public static function filterFormat($format, $excludedFormat = [])
	{
		if ($excludedFormat) {
			foreach ($excludedFormat as $formatSign) {
				$format = preg_replace('/(([^\p{L}\p{N}]{1})?' . preg_quote($formatSign) . ')/u', '', $format);
			}
		}
		$format = trim($format, " ,'.\"");
		return $format;
	}

	public static function getDiffMicro($date1, $date2)
	{
		//Absolute val of Date 1 in seconds from  (EPOCH Time) - Date 2 in seconds from (EPOCH Time)
		$diff = abs(strtotime($date1->format('d-m-Y H:i:s.u')) - strtotime($date2->format('d-m-Y H:i:s.u')));

		//Creates variables for the microseconds of date1 and date2
		$micro1 = $date1->format('u');
		$micro2 = $date2->format('u');

		//Absolute difference between these micro seconds:
		$micro = abs($micro1 - $micro2);

		//Creates the variable that will hold the seconds (?):
		$difference = $diff . '.' . $micro;

		return $difference;
	}
}
