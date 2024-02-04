<?php
class kESearchUtils
{
	const ELASTIC_CLUSTER_NAME = 'elastic_cluster_name';
	const ELASTIC_CLUSTER_NAME_TTL = 30;
	
	public static function getElasticClusterName()
	{
		$retry = 0;
		
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::ELASTIC_CLUSTER_NAME);
		if ($cache)
		{
			$elasticClusterName = $cache->get(self::ELASTIC_CLUSTER_NAME);
			
			if ($elasticClusterName)
			{
				KalturaLog::log("Returning value from memcache [$elasticClusterName]");
				return $elasticClusterName;
			}
		}
		
		$elasticClient = new elasticClient();
		$elasticClusterName = $elasticClient->getElasticClusterName();
		
		while (!$elasticClusterName && $retry < 3)
		{
			$elasticClusterName = $elasticClient->getElasticClusterName();
			$retry++;
			sleep(1);
		}
		
		unset($elasticClient);
		
		if ($cache)
		{
			KalturaLog::log("Storing elastic cluster name [$elasticClusterName] in memcache");
			$cache->set(self::ELASTIC_CLUSTER_NAME, $elasticClusterName, self::ELASTIC_CLUSTER_NAME_TTL);
		}
		
		return $elasticClusterName;
	}
}
