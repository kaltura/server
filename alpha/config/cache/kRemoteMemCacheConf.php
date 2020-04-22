<?php
require_once __DIR__ . '/kBaseMemcacheConf.php';
require_once (__DIR__ . '/../../../infra/general/iniUtils.php');

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
		return $this->loadByHostName(array($mapName), $hostname);
	}

	public function loadByHostName(array $mapNames ,$hostname, $excludeHost = false)
	{
		$maps = array();
		foreach ($mapNames as $mapName)
		{
			$maps = array_merge($maps, $this->getRelevantMapList($mapName, strtolower($hostname), $excludeHost));
		}
		$this->orderMap($maps);
		return $this->mergeMaps($maps);
	}

	protected function getRelevantMapList($requestedMapName , $hostname, $excludeHost = false)
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
						if ($hostname === $hostPattern && $excludeHost)
						{
							continue;
						}

						if ($hostPattern && $hostname != $hostPattern && $hostPattern !== '#')
						{
							$hostPattern = str_replace('#', '.*', $hostPattern);
							if(!preg_match('/' . $hostPattern . '/', $hostname))
							{
								continue;
							}

							// since $hostname not equal to $hostPattern we need to avoid an inner substring
							if(substr( $hostPattern, 0, 2 ) !== '.*' && substr( $hostPattern, -2 ) !== '.*')
							{
								continue;
							}
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
		$cache = $this->getCache();
		if (!$cache)
		{
			return null;
		}
		$sections = array();
		$globalContent = null;
		/** Note: we are concatenating the text content to a single ini file content since some inheritence sections are in
		 * different maps and only after merging them we can create the merged ini file and validate it.
		 * Since there are also global parameters in many merged files we need to merge them get them seperatly before merging the content
		 * otherwise they will be merged to the previous map last section and will not be in global section anymore.
		 */
		foreach ($mapNames as $mapName)
		{
			$map = $cache->get(self::CONF_MAP_PREFIX . $mapName);
			if ($map)
			{
				$mapContent = json_decode($map, true);
				IniUtils::splitContent($mapContent, $globalContent, $sections);
			}
		}
		return IniUtils::iniStringToIniArray($globalContent . PHP_EOL . IniUtils::iniSectionsToString($sections));
	}

	public function getHostList($requesteMapName , $hostNameRegex = null)
	{
		$hostList = array();
		$cache = $this->getCache();
		if(!$cache)
		{
			return $hostList;
		}

		$mapsList = $cache->get(self::MAP_LIST_KEY);
		foreach ($mapsList as $mapName => $version)
		{
			$mapVar = explode(self::MAP_DELIMITER, $mapName);
			$storedMapName = $mapVar[0];
			$hostPattern = isset($mapVar[1]) ? $mapVar[1] : null;
			if ($requesteMapName == $storedMapName)
			{
				if(!$hostNameRegex || preg_match('/'. strtolower($hostNameRegex) .'/' ,$hostPattern ) )
				{
					$hostList[] = $hostPattern;
				}
			}
		}

		return $hostList;
	}
	public function getMap($mapName,$hostName)
	{
		$cache = $this->getCache();
		if(!$cache)
		{
			return null;
		}
		$mapKeyInCache = self::CONF_MAP_PREFIX.$mapName.self::MAP_DELIMITER.$hostName;
		return $cache->get($mapKeyInCache);
	}
}
