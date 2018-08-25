<?php
require_once (__DIR__ . "/cache/cacheConfFactory.php");

class kConfCacheManager
{
	private static $mapLoadFlow		=array(cacheConfFactory::SESSION,cacheConfFactory::APC,cacheConfFactory::LOCAL_MEM_CACHE,cacheConfFactory::FILE_SYSTEM,cacheConfFactory::REMOTE_MEM_CACHE);
	private static $mapStoreFlow	=array(cacheConfFactory::SESSION			=>array(),
											cacheConfFactory::APC				=>array(cacheConfFactory::SESSION),
											cacheConfFactory::LOCAL_MEM_CACHE	=>array(cacheConfFactory::APC,cacheConfFactory::SESSION),
											cacheConfFactory::FILE_SYSTEM		=>array(cacheConfFactory::APC,cacheConfFactory::SESSION),
											cacheConfFactory::REMOTE_MEM_CACHE	=>array(cacheConfFactory::APC,cacheConfFactory::SESSION,cacheConfFactory::LOCAL_MEM_CACHE));

	private static $keyLoadFlow		=array(cacheConfFactory::SESSION,cacheConfFactory::APC,cacheConfFactory::REMOTE_MEM_CACHE);
	private static $keyStoreFlow	=array(cacheConfFactory::SESSION			=>	array(),
											cacheConfFactory::APC 				=> 	array(cacheConfFactory::SESSION),
											cacheConfFactory::REMOTE_MEM_CACHE 	=> 	array(cacheConfFactory::APC,cacheConfFactory::SESSION));

	public static function getMap($mapName)
	{
		return self::load($mapName);
	}

	public static function loadKey()
	{
		foreach (self::$keyLoadFlow as $cacheEntity)
		{
			$cacheObj = cacheConfFactory::getInstance($cacheEntity);
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
			cacheConfFactory::getInstance($cacheEntity)->storeKey($key);
	}

	static function hasMap ($mapName)
	{
		$key=null;
		foreach (self::$mapLoadFlow as $cacheEntity)
		{
			/* @var $cacheObj baseConfCache*/
			$cacheObj = cacheConfFactory::getInstance($cacheEntity);
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
			/* @var $cacheObj baseConfCache*/
			$cacheObj = cacheConfFactory::getInstance($cacheEntity);
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
		cacheConfFactory::getInstance(cacheConfFactory::SESSION) -> store($key, $mapName,array());
		return array();
	}
	static private function store ($key, $mapName, $map, $foundIn)
	{
		$storeFlow = self::$mapStoreFlow[$foundIn];
		foreach ($storeFlow as $cacheEntity)
			cacheConfFactory::getInstance($cacheEntity)->store($key, $mapName, $map);
	}
	static public function getUsage()
	{
		$out = array();
		foreach (self::$mapLoadFlow as $cacheEntity)
		{
			$out['usage'][$cacheEntity] = cacheConfFactory::getInstance($cacheEntity)->getUsageCounter();
			$out['cacheMiss'][$cacheEntity] = cacheConfFactory::getInstance($cacheEntity)->getCacheMissCounter();
		}
		foreach (self::$keyLoadFlow as $cacheEntity)
			$out['getKey'][$cacheEntity] = cacheConfFactory::getInstance($cacheEntity)->getKeyUsageCounter();
		return $out;
	}
	static public function printUsage()
	{
		$str = "Conf usage:";
		foreach (self::$mapLoadFlow as $cacheEntity)
			$str .= $cacheEntity."={". cacheConfFactory::getInstance($cacheEntity)->getUsageCounter()."}";
			$str .= "| Key usage: ";
		foreach (self::$keyLoadFlow as $cacheEntity)
			$str .= $cacheEntity."={". cacheConfFactory::getInstance($cacheEntity)->getKeyUsageCounter()."}";
		$str .= "| Cache Miss: ";
		foreach (self::$mapLoadFlow as $cacheEntity)
			$str .= $cacheEntity."={". cacheConfFactory::getInstance($cacheEntity)->getCacheMissCounter()."}";

			foreach (self::$mapLoadFlow as $cacheEntity)
		{
			$mapStr = cacheConfFactory::getInstance($cacheEntity)->getUsageMap();
			$str .= "\n\r" . $cacheEntity . "=============>" . print_r($mapStr, true);
		}
		KalturaLog::debug($str);
	}
}
