<?php
/**
 * @package infra
 * @subpackage general
 */

class kTimeZoneUtils
{
    /**
     * @param string $datetimeStr
     * @param string $timeZone
     * @return int a timestamp on success
     */
    public static function timeZoneStrToTime($datetimeStr, $timeZone=null)
    {
    	if($timeZone)
    	{
    		$timeZone = new DateTimeZone($timeZone);
    	}
    	$datetime = new DateTime($datetimeStr, $timeZone);
    	return $datetime->getTimestamp();
    }

	/**
	 * @param $timestamp
	 * @param string $timezone
	 * @return DateTime
	 */
	public static function timezoneDateTime($timestamp, $timezone=null)
	{
		$datetime = new DateTime();
		if($timezone)
		{
			$datetime->setTimeZone(new DateTimeZone($timezone));
		}
		$datetime->setTimestamp($timestamp);
		return $datetime;
	}

	/**
	 * @param string $format
	 * @param $timestamp
	 * @param string $timezone
	 * @return string
	 */
	public static function timezoneDate($format, $timestamp, $timezone=null)
	{
		$datetime = self::timezoneDateTime($timestamp, $timezone);
		return $datetime->format($format);
	}

	/**
	 * @param $timestamp
	 * @param string $timezone
	 * @return DateTime
	 */
	public static function midnightTimezoneDateTime($timestamp, $timezone=null)
	{
		$dateTime = self::timezoneDateTime($timestamp, $timezone);
		$dateTime->setTime(0, 0); // set time to midnight
		return $dateTime;
	}

	/**
	 * @param string $datetime
	 * @return int a timestamp on success
	 */
	public static function strToZuluTime($datetime)
	{
		$newDatetime = str_replace(array('T','Z'),array(' ',''), $datetime);
		return self::timeZoneStrToTime($newDatetime, 'Zulu');
	}
}
