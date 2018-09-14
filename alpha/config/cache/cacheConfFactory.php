<?php

class cacheConfFactory
{
	const SESSION = 'SESSION';
	const APC = 'APC';
	const LOCAL_MEM_CACHE = 'LOCAL_MEM_CACHE';
	const FILE_SYSTEM = 'FILE_SYSTEM';
	const REMOTE_MEM_CACHE = 'REMOTE_MEM_CACHE';
	protected static $cacheInstanceList;

	static function register($name, $instance)
	{
		self::$cacheInstanceList[$name]=$instance;
	}

	static function getInstance($name)
	{

		$cacheConfRequireArray = array(
			self::SESSION =>  array('sessionConf','sessionConf.php'),
			self::APC => array('apcConf','apcConf.php'),
			self::LOCAL_MEM_CACHE => array('localMemCacheConf','localMemCacheConf.php'),
			self::FILE_SYSTEM => array('fileSystemConf','fileSystemConf.php'),
			self::REMOTE_MEM_CACHE => array('remoteMemCacheConf','remoteMemCacheConf.php'));

		if(!isset(self::$cacheInstanceList[$name]))
		{
			list ($className , $requireFile) = $cacheConfRequireArray[$name];
			$ret = require_once (__DIR__.'/'.$requireFile);
			self::register($name, new $className);
		}
		return self::$cacheInstanceList[$name];
	}
}

