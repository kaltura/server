<?php
require_once (__DIR__ . "/cache/kCacheConfFactory.php");

class kConfCacheManager
{
	private static $mapLoadFlow	= array(kCacheConfFactory::SESSION,
										kCacheConfFactory::APC,
										kCacheConfFactory::LOCAL_MEM_CACHE,
										kCacheConfFactory::FILE_SYSTEM,
										kCacheConfFactory::REMOTE_MEM_CACHE);

	private static $mapStoreFlow = array(kCacheConfFactory::SESSION	=> array(),
										kCacheConfFactory::APC => array(kCacheConfFactory::SESSION),
										kCacheConfFactory::LOCAL_MEM_CACHE => array(kCacheConfFactory::APC, kCacheConfFactory::SESSION),
										kCacheConfFactory::FILE_SYSTEM => array(kCacheConfFactory::APC, kCacheConfFactory::SESSION),
										kCacheConfFactory::REMOTE_MEM_CACHE	=> array(kCacheConfFactory::APC, kCacheConfFactory::SESSION, kCacheConfFactory::LOCAL_MEM_CACHE));

	private static $keyLoadFlow	= array(kCacheConfFactory::SESSION,
										kCacheConfFactory::APC,
										kCacheConfFactory::REMOTE_MEM_CACHE);

	private static $keyStoreFlow = array(kCacheConfFactory::SESSION	=> array(),
										kCacheConfFactory::APC => array(kCacheConfFactory::SESSION),
										kCacheConfFactory::REMOTE_MEM_CACHE	=> array(kCacheConfFactory::APC, kCacheConfFactory::SESSION));

	private static $mapInitFlow = array(kCacheConfFactory::SESSION,
										kCacheConfFactory::APC,
										kCacheConfFactory::FILE_SYSTEM);

	private static $init=false;

	const KEY_TTL=30;
	const LONG_KEY_TTL=300;


	protected static function initLoad($cacheName)
	{
		foreach (self::$mapInitFlow as $cacheEntity)
		{
			/* @var $cacheObj kBaseConfCache*/
			$cacheObj = kCacheConfFactory::getInstance($cacheEntity);
			$map = $cacheObj->load(null, $cacheName);
			if($map)
			{
				self::store(null, $cacheName, $map, $cacheEntity);
				kCacheConfFactory::getInstance($cacheName);
				return;
			}
		}
	}


	protected static function init()
	{
		if(self::$init  || PHP_SAPI === 'cli')
		{
			return;
		}
		self::$init=true;
		//load basic parameters
		//remote and local memcache	configuration maps
		self::initLoad(kCacheConfFactory::LOCAL_MEM_CACHE);
		self::initLoad(kCacheConfFactory::REMOTE_MEM_CACHE);
	}


	public static function getMap($mapName)
	{
		return self::load($mapName);
	}

	public static function loadKey()
	{
		self::init();

		foreach (self::$keyLoadFlow as $cacheEntity)
		{
			$cacheObj = kCacheConfFactory::getInstance($cacheEntity);
			$ret = $cacheObj->loadKey();
			if($ret)
			{
				$cacheObj->incKeyUsageCounter();
				self::storeKey($ret, $cacheEntity);
				return $ret;
			}
		}
		return null ; //no key is available
	}

	protected static function storeKey($key, $foundIn)
	{
		$remoteCache = kCacheConfFactory::getInstance(kCacheConfFactory::REMOTE_MEM_CACHE);
		$ttl=self::LONG_KEY_TTL;
		if($remoteCache->isActive())
		{
			$ttl=self::KEY_TTL;
		}

		$storeFlow = self::$keyStoreFlow[$foundIn];

		foreach ($storeFlow as $cacheEntity)
			kCacheConfFactory::getInstance($cacheEntity)->storeKey($key,$ttl);
	}

	public static function hasMap ($mapName)
	{
		$map = self::load($mapName);
		return !empty($map);
	}

	static $loadRecursiveLock;

	public static function load ($mapName, $key=null)
	{
		self::init();
		if(self::$loadRecursiveLock)
		{
			return array();
		}
		self::$loadRecursiveLock=true;

		foreach (self::$mapLoadFlow as $cacheEntity)
		{
			/* @var $cacheObj kBaseConfCache*/
			$cacheObj = kCacheConfFactory::getInstance($cacheEntity);
			if(!$key && $cacheObj->isKeyRequired() && PHP_SAPI != 'cli')
				$key = self::loadKey();

			$map = $cacheObj->load($key, $mapName);
			if($map)
			{
				$cacheObj->incUsage($mapName);
				self::store($key, $mapName, $map, $cacheEntity);
				self::$loadRecursiveLock=false;
				return $map;
			}
			$cacheObj->incCacheMissCounter();
		}
		kCacheConfFactory::getInstance(kCacheConfFactory::SESSION) -> store($key, $mapName,array());
		self::$loadRecursiveLock=false;
		return array();
	}

	static protected function store ($key, $mapName, $map, $foundIn)
	{
		$storeFlow = self::$mapStoreFlow[$foundIn];
		foreach ($storeFlow as $cacheEntity)
			kCacheConfFactory::getInstance($cacheEntity)->store($key, $mapName, $map);
	}

	static public function getUsage()
	{
		$out = array();
		foreach (self::$mapLoadFlow as $cacheEntity)
		{
			$out['usage'][$cacheEntity] = kCacheConfFactory::getInstance($cacheEntity)->getUsageCounter();
			$out['cacheMiss'][$cacheEntity] = kCacheConfFactory::getInstance($cacheEntity)->getCacheMissCounter();
		}
		foreach (self::$keyLoadFlow as $cacheEntity)
			$out['getKey'][$cacheEntity] = kCacheConfFactory::getInstance($cacheEntity)->getKeyUsageCounter();
		return $out;
	}

	static public function printUsage()
	{
		$str = "Conf usage:";
		foreach (self::$mapLoadFlow as $cacheEntity)
			$str .= $cacheEntity.'={'. kCacheConfFactory::getInstance($cacheEntity)->getUsageCounter().'}';
			$str .= '| Key usage: ';
		foreach (self::$keyLoadFlow as $cacheEntity)
			$str .= $cacheEntity.'={'. kCacheConfFactory::getInstance($cacheEntity)->getKeyUsageCounter().'}';
		$str .= '| Cache Miss: ';
		foreach (self::$mapLoadFlow as $cacheEntity)
			$str .= $cacheEntity.'={'. kCacheConfFactory::getInstance($cacheEntity)->getCacheMissCounter().'}';

			foreach (self::$mapLoadFlow as $cacheEntity)
		{
			$mapStr = kCacheConfFactory::getInstance($cacheEntity)->getUsageMap();
			$str .= "\n\r" . $cacheEntity . '=============>' . print_r($mapStr, true);
		}
		KalturaLog::debug($str);
	}
}
