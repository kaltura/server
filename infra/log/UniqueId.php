<?php
/**
 * @package infra
 * @subpackage log
 */
class UniqueId
{
	static $_uniqueId = null;
	
	public function __toString()
	{
		return self::get();
	}	
	
	public static function get()
	{
		if (self::$_uniqueId === null)
		{
			self::$_uniqueId = (string)rand();
			// add a the unique id to Apache's internal variable so we can later log it using the %{KalturaLog_UniqueId}n placeholder
			// within the LogFormat apache directive. This way each access_log record can be matched with its kaltura log lines.
			// before setting the apache note name and value, a condition checks if function exists,
			// due to fact that running from command line will not define this function
			if (function_exists('apache_note'))
				apache_note("KalturaLog_UniqueId", self::$_uniqueId);
		}
			
		return self::$_uniqueId;
	}
}

