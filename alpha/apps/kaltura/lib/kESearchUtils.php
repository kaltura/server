<?php
class kESearchUtils
{
	const CLUSTER_NAME_CACHE_KEY = 'elastic_cluster_name';
	const CLUSTER_NAME_CACHE_KEY_TTL = 55;
	
	protected static function getClusterNameCache()
	{
		return kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_ELASTIC_CLUSTER_NAME);
	}
	
	protected static function addClusterNameToCache($key, $value, $expiry = 60)
	{
		$cache = kESearchUtils::getClusterNameCache();
		if (!$cache)
		{
			return;
		}
		
		// using 'add' and not 'set' - to avoid overriding the ttl
		$res = $cache->add($key, $value, $expiry);
		
		if (!$res)
		{
			return;
		}
		
		KalturaLog::debug("Saved elastic cluster_name [$value] in cache");
	}
	
	protected static function getClusterNameFromCache()
	{
		$cache = kESearchUtils::getClusterNameCache();
		if (!$cache)
		{
			return false;
		}
		
		$key = self::CLUSTER_NAME_CACHE_KEY;
		$elasticClusterName = $cache->get($key);
		
		if (!$elasticClusterName)
		{
			KalturaLog::debug("Cache value for key [$key] not found");
			return false;
		}
		
		KalturaLog::debug("Cache value for key [$key] found, value [$elasticClusterName]");
		return $elasticClusterName;
	}
	
	public static function getElasticClusterName()
	{
		$elasticClusterName = kESearchUtils::getClusterNameFromCache();
		if ($elasticClusterName)
		{
			return $elasticClusterName;
		}
		
		$elasticClient = new elasticClient(null, null, null, 500);
		$elasticClusterName = $elasticClient->getElasticClusterName();
		
		$retry = 0;
		
		while (!$elasticClusterName && $retry < 3)
		{
			$retry++;
			
			// check cache again, in case other php process added it
			$elasticClusterName = kESearchUtils::getClusterNameFromCache();
			if ($elasticClusterName)
			{
				break;
			}
			
			$elasticClusterName = $elasticClient->getElasticClusterName();
			if ($elasticClusterName)
			{
				break;
			}
		}
		
		unset($elasticClient);
		
		kESearchUtils::addClusterNameToCache(self::CLUSTER_NAME_CACHE_KEY, $elasticClusterName, self::CLUSTER_NAME_CACHE_KEY_TTL);
		
		return $elasticClusterName;
	}
}
