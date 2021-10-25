<?php
/**
 * @package infra
 * @subpackage general
 */

class kTimeZoneUtils
{
    /**
     * @param string $datetime
     * @param int $baseTimestamp
     * @param string $timeZone
     * @return int|false a timestamp on success, false otherwise
     */
    public static function timeZoneStrToTime($datetime , $baseTimestamp=NULL, $timeZone=NULL)
    {
        $original = date_default_timezone_get();
        if($timeZone)
        {
            date_default_timezone_set($timeZone);
        }
        if($baseTimestamp)
        {
            $result = strtotime($datetime, $baseTimestamp);
        }
        else
        {
            $result = strtotime($datetime);
        }
        date_default_timezone_set($original);
        return $result;
    }

    public static function strToZuluTime($datetime)
    {
        $newDatetime = str_replace(array('T','Z'),array(' ',''), $datetime);
        return self::timeZoneStrToTime($newDatetime, Null, 'Zulu');
    }

}
