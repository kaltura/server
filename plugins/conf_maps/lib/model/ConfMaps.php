<?php


/**
 * Skeleton subclass for representing a row from the 'conf_maps' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class ConfMaps extends BaseConfMaps
{
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;

	/**
	 * @param ConfMaps $exstingMap
	 */
	public function addNewMapVersion(ConfMaps $exstingMap, $content)
	{
		$this->setMapName($exstingMap->getMapName());
		$this->setHostName($exstingMap->getHostName());
		$this->setVersion($exstingMap->getVersion() + 1);
		$this->setContent($content);
		$this->setRemarks(kCurrentContext::$ks);
		$this->setStatus($exstingMap->getStatus());
		$this->save();
	} // ConfMaps

	function syncMapsToCache()
	{
		$mapNameInCache = self::getMapNameInCache($this->getMapName() , $this->getHostName());
		$memcacheObjects = self::getMemcacheObjects();
		foreach ($memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kBaseConfCache::CONF_MAP_PREFIX.$mapNameInCache,$this->getContent());
		}
		$mapListInCache = $memcacheObjects[0]->get(kRemoteMemCacheConf::MAP_LIST_KEY);
		$mapListInCache[$mapNameInCache] = $this->getVersion();
		$mapListInCache['UPDATED_AT']=date("Y-m-d H:i:s");
		foreach ($memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kRemoteMemCacheConf::MAP_LIST_KEY, $mapListInCache);
		}
		//create new key and set all memcache
		$chacheKey = kBaseConfCache::generateKey();
		foreach ($memcacheObjects as $memcacheObject)
		{
			$memcacheObject->set(kBaseConfCache::CONF_CACHE_VERSION_KEY, $chacheKey);
		}
	}
	/**
	 * init list of memcache objects that can be used to read / write
	 * @throws Exception
	 */
	protected static function getMemcacheObjects()
	{
		$memcacheObjects = array();
		$remoteCacheMap = kConf::getMap('kRemoteMemCacheConf');
		if(!isset($remoteCacheMap['write_address_list']) || !isset($remoteCacheMap['port']))
		{
			throw new Exception('Missing configuration , cannot load cache objects');
		}
		$port = $remoteCacheMap['port'];
		$memcacheList = $remoteCacheMap['write_address_list'];
		foreach($memcacheList as $memcacheItem)
		{
			$cacheObject = new kInfraMemcacheCacheWrapper();
			if(!$cacheObject->init(array('host'=>$memcacheItem ,'port'=>$port)))
			{
				throw new Exception('Cannot open connection to memcache host:{$memcacheItem} port:{$port}');
			}
			$memcacheObjects[] = $cacheObject;
		}
		return $memcacheObjects;
	}

	/**
	 * Build special keyword of map name in cache
	 * @param $mapName name of the map
	 * @param $hostNameRegex regex of hosts using # istead of *
	 * @return string
	 */
	protected static function getMapNameInCache($mapName , $hostNameRegex)
	{
		return $mapName . kRemoteMemCacheConf::MAP_DELIMITER . $hostNameRegex;
	}
}

