<?php
require_once __DIR__ . '/kBaseConfCache.php';
require_once __DIR__ . '/kMapCacheInterface.php';

class kBaseMemcacheConf extends kBaseConfCache implements kMapCacheInterface
{
	protected $cache;
	protected $inLoad;
	protected $confParams;

	public function getCache()
	{
		if(!$this->cache)
		{
			$this->cache = $this->initCache();
		}
		return $this->cache;
	}

	function isActive()
	{
		return !is_null($this->cache);
	}

	function __construct()
	{
		$this->cache=null;
		$this->confParams = $this->getConfigParams(get_class($this));
	}

	protected function getConfigParams($mapName)
	{
		$map = kConfCacheManager::load($mapName,$mapName);
		return $map;
	}

	protected function initCache()
	{
		require_once (__DIR__ . '/../../../infra/cache/kInfraMemcacheCacheWrapper.php');
		$cache = new kInfraMemcacheCacheWrapper;
		try
		{
			if (!$cache->init($this->confParams))
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
			return $cache->get(self::CONF_MAP_PREFIX.$mapName);
		return null;
	}

	public function store($key, $mapName, $map, $ttl = 0)
	{
		$cache = $this->getCache();
		if($cache)
			return $cache->set(self::CONF_MAP_PREFIX.$mapName, $map); // try to fetch from cache
		return null;
	}

	public function delete($key, $mapName)
	{
		$cache = $this->getCache();
		if($cache)
			return $cache->delete(self::CONF_MAP_PREFIX.$mapName);
		return false;
	}
}
