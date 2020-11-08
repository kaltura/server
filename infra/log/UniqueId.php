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
			{
				header('X-Kaltura-Session: ' . self::$_uniqueId . ', ' . time(), false);

				if (function_exists('apache_note'))
				{
					apache_note('Kaltura_SessionId', self::$_uniqueId);
				}
			}
		}
			
		return self::$_uniqueId;
	}
}

