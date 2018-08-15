<?php

require_once __DIR__."/baseConfCache.php";
require_once __DIR__."/mapCacheInterface.php";
require_once __DIR__."/keyCacheInterface.php";

class sessionConf extends baseConfCache implements mapCacheInterface , keyCacheInterface
{
	protected static $map = array();
	protected static $cacheKey=null;
	public function load($key,$mapName)
	{
		if(isset(self::$map[$mapName]))
			return self::$map[$mapName];
		return false;
	}
	public function hasMap($key,$mapName){return isset(self::$map[$mapName]);}
	public function store($key,$mapName,$map,$ttl=0){self::$map[$mapName] = $map;}
	public function deleteMap($key,$mapName){unset(self::$map[$mapName]);}
	public function loadKey(){return self::$cacheKey;}
	public function storeKey($key, $ttl = 30){self::$cacheKey = $key;}
	public function deleteKey(){self::$cacheKey = null;}

}
