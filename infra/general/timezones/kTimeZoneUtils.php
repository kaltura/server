<?php
/**
 * @package infra
 * @subpackage general
 */

class kTimeZoneUtils
{
    /**
     * @param string $datetime
     * @param string $timeZone
     * @return int|false a timestamp on success, false otherwise
     */
    public static function timeZoneStrToTime($datetimeStr, $timeZone=null)
    {
    	$datetime = new DateTime($datetimeStr, new DateTimeZone($timeZone));
    	return $datetime->getTimestamp();
    }

    public static function strToZuluTime($datetime)
    {
        $newDatetime = str_replace(array('T','Z'),array(' ',''), $datetime);
        return self::timeZoneStrToTime($newDatetime, 'Zulu');
    }

    public static function timezoneDate($format, $timestamp, $timezone=null)
	{
		$datetime = new DateTime();
		$datetime->setTimeZone(new DateTimeZone($timezone));
		$datetime->setTimestamp($timestamp);
		return $datetime->format($format);
	}

}
