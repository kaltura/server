<?php
/**
 * @package infra
 * @subpackage log
 */
class kLog
{
	private static $s_context;
	
	private static $logger = null;
	
	public static function setContext ( $prefix )
	{
		self::$s_context = $prefix;	
	}
	
	// TODO - allow mor args - add to string at the end
	public static function log ( $str )
	{
		if ( self::$s_context )
			self::getLogger()->log ( self::$s_context . " " . $str );
		else
			self::getLogger()->log ( $str );
			
		global $g_context;
		if (isset($g_context))
			TRACE($str);
	}
	
	public static function setLogger ( $logger )
	{
		self::$logger = $logger;	
	}
	
	private static function getLogger ()
	{
		if ( ! self::$logger )
		{
			if(class_exists('sfLogger'))
				self::$logger = sfLogger::getInstance();
			else
				self::$logger = KalturaLog::getInstance();
		}
		return self::$logger;
	}
}
