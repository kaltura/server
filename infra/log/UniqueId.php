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
			if (php_sapi_name() !== 'cli')
				header('X-Kaltura-Session:'.self::$_uniqueId, false);
		}
			
		return self::$_uniqueId;
	}
}

