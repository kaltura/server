<?php
require_once __DIR__ . '/kBaseMemcacheConf.php';

class kRemoteMemCacheConf extends kBaseMemcacheConf implements kKeyCacheInterface,kMapCacheInterface
{
	const MAP_LIST_KEY = 'MAP_LIST_KEY';
	const MAP_DELIMITER = '|';

	public function loadKey()
	{
		$key=null;
		$cache = $this->getCache();
		if($cache)
			$key = $cache->get(kBaseConfCache::CONF_CACHE_VERSION_KEY);

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
		$mapsList = null;
		$cache = $this->getCache();
		if($cache)
		{
			$mapsList = $cache->get(self::MAP_LIST_KEY);
			if ($mapsList)
			{
				foreach ($mapsList as $mapName => $version)
				{
					$mapVar = explode(self::MAP_DELIMITER, $mapName);
					$storedMapName = $mapVar[0];
					$hostPattern = isset($mapVar[1]) ? $mapVar[1] : null;
					if ($requestedMapName == $storedMapName)
					{
						if ($hostPattern && $hostname != $hostPattern && $hostPattern !== '#')
						{
							$hostPattern = str_replace('#', '*', $hostPattern);
							if(!preg_match('/' . $hostPattern . '/', $hostname))
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
		{
			return null;
		}
		foreach ($mapNames as $mapName)
		{
			$map = $cache->get($mapName);
			if($map)
			{
				$map = json_decode($map,true);
				if($mergedMaps)
				{
					$mergedMaps = kEnvironment::mergeConfigItem($mergedMaps, $map);
				}
				else
				{
					$mergedMaps = $map;
				}
			}
		}
		return $mergedMaps;
	}
}
