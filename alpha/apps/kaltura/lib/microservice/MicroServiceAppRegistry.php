<?php
/**
 * App Registry Micro Service
 */
class MicroServiceAppRegistry extends MicroServiceBaseService
{
	const APP_GUID_CACHE_KEY_PREFIX = 'appGuid_';
	const APP_GUID_CACHE_TTL = 86400; // cached for 1 day
	
	public function __construct()
	{
		parent::__construct('app-registry','app-registry');
	}
	
	/**
	 * @throws kCoreException
	 */
	public function get($partnerId, $appGuid)
	{
		return $this->serve($partnerId,'get', array('id' => $appGuid));
	}

	public function list($partnerId, $filter, $pager = array())
	{
		return $this->serve($partnerId,'list', array('filter' => $filter, 'pager' => $pager));
	}
	
	/**
	 * @throws kCoreException
	 */
	public static function getExistingAppGuid($partnerId, $appGuid)
	{
		$appGuidExists = MicroServiceAppRegistry::getAppGuidFromCache($appGuid);
		if ($appGuidExists)
		{
			return $appGuid;
		}
		
		$appRegistryClient = new MicroServiceAppRegistry();
		$appRegistry = $appRegistryClient->get($partnerId, $appGuid);
		
		if (isset($appRegistry->code) && $appRegistry->code == 'OBJECT_NOT_FOUND')
		{
			return false;
		}
		
		if (!isset($appRegistry->id) || !kString::isValidMongoId($appRegistry->id))
		{
			return false;
		}
		
		MicroServiceAppRegistry::addAppGuidToCache($appRegistry->id);
		return $appRegistry->id;
	}
	
	/**
	 * @throws kCoreException
	 */
	public static function getExistingAppGuidsFromCsv($partnerId, $appGuidsCsv)
	{
		$appGuidsResponse = array();
		$appGuidsCached = array();
		$appGuidsToQuery = explode(',', $appGuidsCsv);
		
		// remove duplicate appGuids, if exists
		$appGuidsToQuery = array_unique($appGuidsToQuery);
		
		// filter appGuids that already stored in cache
		foreach ($appGuidsToQuery as $key => $appGuid)
		{
			$appGuidExist = MicroServiceAppRegistry::getAppGuidFromCache($appGuid);
			
			// remove appGuids that already stored in cache before querying app-registry ms
			if ($appGuidExist)
			{
				$appGuidsCached[] = $appGuid;
				unset($appGuidsToQuery[$key]);
			}
		}
		
		// reset array keys or else 'json_encode' will encode the non-consecutive as the key-value resulting in bad mongo query
		$appGuidsToQuery = array_values($appGuidsToQuery);
		
		// if all appGuids were found in cache, return original csv
		if (!count($appGuidsToQuery))
		{
			return $appGuidsCsv;
		}
		
		$filter = array(
			'idIn' => $appGuidsToQuery
		);
		
		$pager = array(
			'offset' => 0,
			'limit' => count($appGuidsToQuery)
		);
		
		$appRegistryClient = new MicroServiceAppRegistry();
		$appRegistries = $appRegistryClient->list($partnerId, $filter, $pager);
		
		foreach ($appRegistries->objects as $appRegistry)
		{
			if (isset($appRegistry->id))
			{
				$appGuidsResponse[] = $appRegistry->id;
				MicroServiceAppRegistry::addAppGuidToCache($appRegistry->id);
			}
		}
		
		$appGuidsFinal = array_merge($appGuidsCached, $appGuidsResponse);
		return count($appGuidsFinal) ? implode(',', $appGuidsFinal) : 'null'; // set to 'null' will not return results (if set to '' 'attachToCriteria' will remove it from mysql query)
	}
	
	/**
	 * @throws kCoreException
	 */
	private static function getAppGuidFromCache($appGuid)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_MICROSERVICES);
		if (!$cache)
		{
			throw new kCoreException('[Failed to instantiate cache]', kCoreException::FAILED_TO_INSTANTIATE_MICROSERVICE_CACHE);
		}
		
		$cacheKey = self::APP_GUID_CACHE_KEY_PREFIX . $appGuid;
		$cacheValue = $cache->get($cacheKey);
		if (!$cacheValue)
		{
			KalturaLog::debug("Cache value for key [$cacheKey] not found");
			return false;
		}
		
		KalturaLog::debug("Cache value for key [$cacheKey] found, value [$cacheValue]");
		return $cacheValue;
	}
	
	/**
	 * @throws kCoreException
	 */
	private static function addAppGuidToCache($appGuid)
	{
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_MICROSERVICES);
		if (!$cache)
		{
			throw new kCoreException('[Failed to instantiate cache]', kCoreException::FAILED_TO_INSTANTIATE_MICROSERVICE_CACHE);
		}
		
		$cacheKey = self::APP_GUID_CACHE_KEY_PREFIX . $appGuid;
		$res = $cache->add($cacheKey, true, self::APP_GUID_CACHE_TTL);
		
		if (!$res)
		{
			KalturaLog::debug("Failed to save key [$cacheKey] to cache - already stored?");
		}
		
		KalturaLog::debug("Saved key [$cacheKey] to cache");
	}
}
