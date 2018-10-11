<?php
require_once __DIR__ . '/kBaseConfCache.php';
require_once __DIR__ . '/kMapCacheInterface.php';

class kBaseMemcacheConf extends kBaseConfCache implements kMapCacheInterface
{
	protected $cache;
	protected $inLoad;

	protected function getCache()
	{
		return $this->cache;
	}

	function __construct()
	{
		$this->cache=null;
		$confParams = $this->getConfigParams(get_class($this));
		if($confParams)
		{
			$port = $confParams['port'];
			$host = $confParams['host'];
			$this->cache = $this->initCache($port, $host);
		}
	}

	protected function getConfigParams($mapName)
	{
		$map = kConfCacheManager::load($mapName,$mapName);
		return $map;
	}

	protected function initCache($port, $host)
	{
		require_once (__DIR__ . '/../../../infra/cache/kMemcacheCacheWrapper.php');
		$cache = new kMemcacheCacheWrapper;
		$sectionConfig= array('host'=>$host,'port'=>$port);
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
