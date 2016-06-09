<?php
/**
 * @package infra
 * @subpackage log
 */
class KalturaLog
{
    const EMERG   = 'EMERG';
    const ALERT   = 'ALERT';
    const CRIT    = 'CRIT';
    const ERR     = 'ERR';
    const WARN    = 'WARN';
    const NOTICE  = 'NOTICE';
    const INFO    = 'INFO';
    const DEBUG   = 'DEBUG';
    
	static function log($message, $priority = self::NOTICE)
	{
		echo "$priority: $message\n";
	}
	
	static function alert($message)
	{
		self::log($message, self::ALERT);
	}

	static function crit($message)
	{
		self::log($message, self::CRIT);
	}

	static function err($message)
	{
		self::log($message, self::ERR);
	}	

	static function warning($message)
	{
		self::log($message, self::WARN);
	}

	static function notice($message)
	{
		self::log($message, self::NOTICE);
	}	

	static function info($message)
	{
		self::log($message, self::INFO);
	}

	static function debug($message)
	{
		self::log($message, self::DEBUG);
	}
}
