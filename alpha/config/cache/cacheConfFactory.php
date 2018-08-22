<?php
require_once (__DIR__."/apcConf.php");
require_once (__DIR__."/localCache.php");
require_once (__DIR__."/remoteCacheSource.php");
require_once (__DIR__."/sessionConf.php");
require_once (__DIR__."/localStorageConf.php");


class cacheConfFactory
{
	static private $cacheInstanceList=array();

	static function register($name,$instance)
	{
		self::$cacheInstanceList[$name]=$instance;
	}
	static function getInstance($className)
	{
		if(!isset(self::$cacheInstanceList[$className]))
			self::register($className, new $className);
		return self::$cacheInstanceList[$className];
	}
}

