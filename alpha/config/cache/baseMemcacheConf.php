<?php
require_once __DIR__."/baseConfCache.php";
require_once __DIR__."/mapCacheInterface.php";

class baseMemcacheConf extends baseConfCache implements mapCacheInterface
{
	protected $cache;

	protected function getCache()
	{
		return $this->cache;
	}

	function __construct($port, $host)
	{
		$this->cache = $this->initCache($port, $host);
	}

	protected function getConfigParams($sectionName)
	{
		$configFile = kEnvironment::getConfigDir() . '/configCacheParams.php';
		if (file_exists($configFile))
		{
			include($configFile);
			if (isset($cacheConfigParams[$sectionName]))
				return $cacheConfigParams[$sectionName];
		}
		return null;
	}

	protected function initCache($port, $host)
	{
		require_once (__DIR__ . '/../../../infra/cache/kMemcacheCacheWrapper.php');
		$cache = new kMemcacheCacheWrapper;
		$sectionConfig= array("host"=>$host,'port'=>$port);
		try
		{
			if (!$cache->init($sectionConfig))
				$cache = null;
		}
		catch (Exception $e)
		{
			$cache=null;
		}
		return $cache;
	}

	public function load($key, $mapName)
	{
		$cache = $this->getCache();
		if($cache)
			return $cache->get($mapName);
		return null;
	}

	public function store($key, $mapName, $map, $ttl = 0)
	{
		$cache = $this->getCache();
		if($cache)
			return $cache->set($mapName, $map); // try to fetch from cache
		return null;
	}

	public function delete($key, $mapName)
	{
		$cache = $this->getCache();
		if($cache)
			return $cache->delete($mapName);
		return false;
	}
}
