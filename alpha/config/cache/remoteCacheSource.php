<?php
require_once __DIR__."/baseMemcacheConf.php";
class remoteCacheSource extends baseMemcacheConf implements keyCacheInterface
{
	const MAP_LIST_KEY='MAP_LIST_KEY';
	function __construct()
	{
		if(include (kEnvironment::getConfigDir().'/configCacheParams.php'))
		{
			if (isset($remoteCacheSourceConfiguration))
			{
				$port = $remoteCacheSourceConfiguration['port'];
				$host = $remoteCacheSourceConfiguration['host'];
				return parent::__construct($port, $host);
			}
		}
	}
	public function loadKey()
	{
		$key=null;

		if( $cache = $this->getCache() )
			$key = $cache->get(baseConfCache::CONF_CACHE_VERSION_KEY);

		if (!$key)
			$key = self::generateKey();

		//key must be supplied.
		return $key;
	}
	public function storeKey($key,$ttl=30){return;}
	public function load($key, $mapName)
	{
		$hostname = $this->getHostName();
		$mapNames = $this->getRelevantMapList($mapName,$hostname);
		$this->orderMap($mapNames);
		return $this->mergeMaps($mapNames);
	}
	private function getRelevantMapList($requestedMapName , $hostname)
	{
		$filteredMapsList = array($requestedMapName);
		$mapsList=null;
		if($cache = $this->getCache())
		{
			if ($mapsList = $cache->get(self::MAP_LIST_KEY))
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
	private function mergeMaps($mapNames)
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
