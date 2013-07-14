<?php
/**
 * @package server-infra
 * @subpackage log
 */
class LogIp
{
	static $_ip = null;
	
	public function __toString()
	{
		return self::get();
	}
	
	public static function get()
	{
		if (self::$_ip === null)
		{
			try {
				self::$_ip = (string)infraRequestUtils::getRemoteAddress();
			}
			catch (Exception $ex)
			{
				self::$_ip = '';
			}
		}
			
		return self::$_ip;
	}
}
