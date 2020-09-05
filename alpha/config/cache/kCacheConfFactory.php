<?php

class kCacheConfFactory
{
	const SESSION = 'kSessionConf';
	const APC = 'kApcConf';
	const LOCAL_MEM_CACHE = 'kLocalMemCacheConf';
	const FILE_SYSTEM = 'kFileSystemConf';
	const REMOTE_MEM_CACHE = 'kRemoteMemCacheConf';

	protected static $cacheInstanceList;

	static function close()
	{
		self::$cacheInstanceList = array();
	}

	static function register($name, $instance)
	{
		self::$cacheInstanceList[$name]=$instance;
	}

	static function getInstance($name)
	{
		$cacheConfRequireArray = array(
			self::SESSION => self::SESSION ,
			self::APC => self::APC ,
			self::LOCAL_MEM_CACHE => self::LOCAL_MEM_CACHE ,
			self::FILE_SYSTEM => self::FILE_SYSTEM,
			self::REMOTE_MEM_CACHE => self::REMOTE_MEM_CACHE);

		if(!isset(self::$cacheInstanceList[$name]))
		{
			$className = $cacheConfRequireArray[$name];
			require_once (__DIR__.'/'.$className.'.php');
			self::register($name, new $className);
		}
		return self::$cacheInstanceList[$name];
	}
}

