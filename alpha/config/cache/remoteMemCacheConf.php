<?php
require_once __DIR__."/baseMemcacheConf.php";

class remoteMemCacheConf extends baseMemcacheConf implements keyCacheInterface,mapCacheInterface
{
	const MAP_LIST_KEY='MAP_LIST_KEY';

	function __construct()
	{
		$confParams = parent::getConfigParams('remoteMemCacheConf');
		if($confParams)
		{
			$port = $confParams['port'];
			$host = $confParams['host'];
			return  parent::__construct($port, $host);
		}
		$this->cache=null;
	}

	public function loadKey()
	{
		$key=null;
		$cache = $this->getCache();
		if($cache)
			$key = $cache->get(baseConfCache::CONF_CACHE_VERSION_KEY);

		if (!$key)
			$key = self::generateKey();

		//key must be supplied.
		return $key;
	}

	public function storeKey($key, $ttl=30){return;}

	public function load($key, $mapName)
	{
		$hostname = $this->getHostName();
		$mapNames = $this->getRelevantMapList($mapName, $hostname);
		$this->orderMap($mapNames);
		return $this->mergeMaps($mapNames);
	}

	protected function getRelevantMapList($requestedMapName , $hostname)
	{
		$filteredMapsList = array($requestedMapName);
		$mapsList=null;
		$cache = $this->getCache();
		if($cache)
		{
			$mapsList = $cache->get(self::MAP_LIST_KEY);
			if ($mapsList)
			{
				foreach ($mapsList as $mapName =>$version)
				{
					$mapVar = explode('_', $mapName);
					$storedMapName = $mapVar[0];
					$hostPattern = isset($mapVar[1]) ? $mapVar[1] : null;
					if ($requestedMapName == $storedMapName)
					{
						if ($hostPattern && $hostname != $hostPattern)
						{
							$hostPattern = str_replace('#', '*', $hostPattern);
							if(!preg_match('/'.$hostPattern.'/', $hostname))
								continue;
						}
						$filteredMapsList[] = $mapName;
					}
				}
			}
		}
		return $filteredMapsList;
	}

	protected function mergeMaps($mapNames)
	{
		$mergedMaps = array();
		$cache = $this->getCache();
		if(!$cache)
			return null;
		foreach ($mapNames as $mapName)
		{
			$map = $cache->get($mapName);
			if($map)
			{
				$map = json_decode($map,true);
				$mergedMaps = kEnvironment::mergeConfigItem($mergedMaps, $map);
			}
		}
		return $mergedMaps;
	}
}
