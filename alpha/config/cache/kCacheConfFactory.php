<?php

class kCacheConfFactory
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
			self::SESSION =>  array('kSessionConf','kSessionConff.php'),
			self::APC => array('kApcConf','kApcConf.phpp'),
			self::LOCAL_MEM_CACHE => array('kLocalMemCacheConf','kLocalMemCacheConf.phpp'),
			self::FILE_SYSTEM => array('kFileSystemConf','kFileSystemConff.php'),
			self::REMOTE_MEM_CACHE => array('kRemoteMemCacheConf','kRemoteMemCacheConff.php'));

		if(!isset(self::$cacheInstanceList[$name]))
		{
			list ($className , $requireFile) = $cacheConfRequireArray[$name];
			$ret = require_once (__DIR__.'/'.$requireFile);
			self::register($name, new $className);
		}
		return self::$cacheInstanceList[$name];
	}
}

