<?php
require_once (__DIR__ . "/cache/kCacheConfFactory.php");

class kConfCacheManager
{
	private static $mapLoadFlow	=array(kCacheConfFactory::SESSION,kCacheConfFactory::APC,kCacheConfFactory::LOCAL_MEM_CACHE,kCacheConfFactory::FILE_SYSTEM,kCacheConfFactory::REMOTE_MEM_CACHE);
	private static $mapStoreFlow	=array(kCacheConfFactory::SESSION		=>array(),
						kCacheConfFactory::APC			=>array(kCacheConfFactory::SESSION),
						kCacheConfFactory::LOCAL_MEM_CACHE	=>array(kCacheConfFactory::APC,kCacheConfFactory::SESSION),
						kCacheConfFactory::FILE_SYSTEM		=>array(kCacheConfFactory::APC,kCacheConfFactory::SESSION),
						kCacheConfFactory::REMOTE_MEM_CACHE	=>array(kCacheConfFactory::APC,kCacheConfFactory::SESSION,kCacheConfFactory::LOCAL_MEM_CACHE));

	private static $keyLoadFlow	=array(kCacheConfFactory::SESSION,kCacheConfFactory::APC,kCacheConfFactory::REMOTE_MEM_CACHE);
	private static $keyStoreFlow	=array(kCacheConfFactory::SESSION		=>array(),
						kCacheConfFactory::APC 			=>array(kCacheConfFactory::SESSION),
						kCacheConfFactory::REMOTE_MEM_CACHE	=>array(kCacheConfFactory::APC,kCacheConfFactory::SESSION));

	public static function getMap($mapName)
	{
		return self::load($mapName);
	}

	public static function loadKey()
	{
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

	static function storeKey($key, $foundIn)
	{
		$storeFlow = self::$keyStoreFlow[$foundIn];
		foreach ($storeFlow as $cacheEntity)
			kCacheConfFactory::getInstance($cacheEntity)->storeKey($key);
	}

	static function hasMap ($mapName)
	{
		$key=null;
		foreach (self::$mapLoadFlow as $cacheEntity)
		{
			/* @var $cacheObj kBaseConfCache*/
			$cacheObj = kCacheConfFactory::getInstance($cacheEntity);
			if(!$key && $cacheObj->isKeyRequired())
				if($cacheObj->hasMap($key, $mapName))
					return true;
		}
		return false;
	}

	static function load ($mapName, $key=null)
	{
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
				return $map;
			}
			$cacheObj->incCacheMissCounter();
		}
		kCacheConfFactory::getInstance(kCacheConfFactory::SESSION) -> store($key, $mapName,array());
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
