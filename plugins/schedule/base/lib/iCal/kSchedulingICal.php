<?php

class kSchedulingICal
{
	const TIME_FORMAT = 'Ymd\THis\Z';
	const TIME_FORMAT_NO_TIME_ZONE = 'Ymd\THis';
	const TIME_PARSE = '%Y%m%dT%H%i%sZ';
	const TIME_PARSE_NO_TIME_ZONE = '%Y%m%dT%H%i%s';

	const METHOD_CANCEL = 'CANCEL';

	const TYPE_CALENDAR = 'VCALENDAR';
	const TYPE_EVENT = 'VEVENT';

	public static $timezoneMap = null;
	public static $timezoneMapNames = array("windowsTimezones", "lotusTimezones", "phpTimezones", "microsoftExchangeTimezones");

	/**
	 * @param string $data
	 * @param KalturaScheduleEventType $eventsType
	 * @return kSchedulingICalCalendar
	 */
	public static function parse($data, $eventsType)
	{
		return new kSchedulingICalCalendar($data, $eventsType);
	}

	/**
	 * @param string $type
	 * @param array $lines
	 * @return kSchedulingICalComponent
	 * @throws Exception
	 */
	public static function parseComponent($type, array &$lines)
	{
		$component = null;

		switch ($type)
		{
			case self::TYPE_CALENDAR:
				$component = new kSchedulingICalCalendar();
				break;

			case self::TYPE_EVENT:
				$component = new kSchedulingICalEvent();
				break;

			default:
				KalturaLog::warning("Component type [$type] is not supported. Component is ignored during schedulingICal parsing.");

		}

		if ($component != null)
		{
			$component->parseLines($lines);
		}
		return $component;
	}

	public static function formatDate($time, $timeZoneId = null)
	{
		$original = date_default_timezone_get();
		if ($timeZoneId)
		{
			date_default_timezone_set($timeZoneId);
			$date = date(kSchedulingICal::TIME_FORMAT_NO_TIME_ZONE, $time);
		}
		else
		{
			date_default_timezone_set('UTC');
			$date = date(kSchedulingICal::TIME_FORMAT, $time);
		}
		date_default_timezone_set($original);
		return $date;
	}

	public static function parseDate($str, $timezoneFormat = null)
	{
		$replace = array(
			'%Y' => '([1-2][0-9]{3})',
			'%m' => '([0-1][0-9])',
			'%d' => '([0-3][0-9])',
			'%H' => '([0-2][0-9])',
			'%i' => '([0-5][0-9])',
			'%s' => '([0-5][0-9])',
			//			'%T' => '([A-Z]{3})',
		);

		$arr = null;
		if (isset($timezoneFormat))
			$pattern = self::TIME_PARSE_NO_TIME_ZONE;
		else
			$pattern = self::TIME_PARSE;

		if (!preg_match_all('/%([YmdTHis])/', $pattern, $arr))
			return false;

		$fields = $arr[1];
		$regex = '/' . str_replace(array_keys($replace), $replace, $pattern) . '/';

		if (!preg_match($regex, $str, $values))
		{
			return null;
		}

		$hour = 0;
		$minute = 0;
		$second = 0;
		$month = 0;
		$day = 0;
		$year = 0;
		$is_dst = 0;

		foreach ($fields as $index => $field)
		{
			$value = $values[$index + 1];

			switch ($field)
			{
				case 'Y':
					$year = intval($value);
					break;

				case 'm':
					$month = intval($value);
					break;

				case 'd':
					$day = intval($value);
					break;

				case 'H':
					$hour = intval($value);
					break;

				case 'i':
					$minute = intval($value);
					break;

				case 's':
					$second = intval($value);
					break;

				//				case 'T':
				//					$date = date_parse($value);
				//					$hour -= ($date['zone'] / 60);
				//					break;

			}
		}

		$ret = gmmktime($hour, $minute, $second, $month, $day, $year);
		if ($ret)
		{
			$timezone = self::getTimeZoneFormat($timezoneFormat);
			if ($timezone)
			{
				$val = $ret - $timezone->getOffset(new DateTime());
				return $val;
			}
			return null;
		}
		return null;
	}

	public static function getTimeZoneFormat($tzid)
	{
		if (is_null($tzid))
		{
			return new DateTimeZone(date_default_timezone_get());
		}

		KalturaLog::info("Retrieving Timezone according to timezone name: [$tzid]");
		$tzIdentifiers = DateTimeZone::listIdentifiers();
		try
		{
			if ((in_array($tzid, $tzIdentifiers)) || (preg_match('/^GMT(\+|-)([0-9]{4})$/', $tzid, $matches)) || (in_array($tzid, self::getIdentifiersBC())))
			{
				return new DateTimeZone($tzid);
			}
		} catch (Exception $e)
		{    // Catch to continue and check other timezone formats
		}

		foreach (self::$timezoneMapNames as $timezoneMap)
		{
			self::loadTzMaps($timezoneMap);

			// Next, we check if the tzid is somewhere in our tzid map.
			if (isset(self::$timezoneMap[$tzid]))
			{
				return new DateTimeZone(self::$timezoneMap[$tzid]);
			}
		}

		// If we got all the way here, we default to UTC.
		KalturaLog::warning("No Timezone conversion was found for: [$tzid] using Default UTC timezone");
		return new DateTimeZone(date_default_timezone_get());

	}

	/*
	 * @return array
	 */
	public static function getIdentifiersBC()
	{
		return array_merge(include __DIR__ . '/../../../../../infra/general/timezones/timezoneIdentifiers.php');
	}

	/**
	 * This method will load in all the tz mapping information, if it's not yet
	 * done.
	 */
	public static function loadTzMaps($timezoneMapName)
	{
		self::$timezoneMap = array_merge(
			include __DIR__ . '/../../../../../infra/general/timezones/' . $timezoneMapName . '.php');
	}

// Prepare date format for ICS (e.g. 20240925T115352Z)
	public static function formatTransitionDate($time)
	{
		return gmdate(self::TIME_FORMAT_NO_TIME_ZONE, $time);
	}
}
