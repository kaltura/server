<?php

/**
 * @package server-infra
 * @subpackage cache
 */
class kCacheManager
{
	// cache types
	const CACHE_TYPE_PLAY_MANIFEST = 'playManifest';
	const CACHE_TYPE_FILE_SYNC = 'fileSync';
	const CACHE_TYPE_PERMISSION_MANAGER = 'permissionManager';
	const CACHE_TYPE_QUERY_CACHE_KEYS = 'queryCacheKeys';
	const CACHE_TYPE_QUERY_CACHE_QUERIES = 'queryCacheQueries';
	const CACHE_TYPE_PS2 = 'ps2Cache';
	const CACHE_TYPE_API_V3 = 'apiV3Cache';
	const CACHE_TYPE_API_V3_FEED = 'apiV3Feed';
	const CACHE_TYPE_FEED_ENTRY = 'feedEntry';
	const CACHE_TYPE_API_EXTRA_FIELDS = 'apiExtraFieldsCache';
	const CACHE_TYPE_SPHINX_STICKY_SESSIONS = 'sphinxStickySessions';
	const CACHE_TYPE_LOCK_KEYS = 'lockKeys';
	const CACHE_TYPE_API_WARMUP = 'apiWarmup';
	const CACHE_TYPE_KWIDGET_SWF = 'kwidgetSwf';
	const CACHE_TYPE_LIVE_MEDIA_SERVER = 'liveMediaServer';
	const CACHE_TYPE_PARTNER_SECRETS = 'partnerSecrets';
	const CACHE_TYPE_SPHINX_EXECUTED_SERVER = 'sphinxExecutedServer';
	const CACHE_TYPE_RESPONSE_PROFILE = 'responseProfile';
	const CACHE_TYPE_RESPONSE_PROFILE_INVALIDATION = 'responseProfileInvalidation';
	const CACHE_TYPE_BATCH_JOBS = 'batchJobs';
	const CACHE_TYPE_RESOURCE_RESERVATION = 'resourceReservation';
	const CACHE_TYPE_ELASTIC_EXECUTED_CLUSTER = 'elasticExecutedCluster';
	const CACHE_TYPE_CACHE_ONLY_ACTIONS = 'cacheOnlyActions';
	const CACHE_TYPE_PLAYKIT_JS = 'playKitJs';
	const CACHE_TYPE_PLAYKIT_JS_SOURCE_MAP = 'playKitJsSourceMap';
	const CACHE_TYPE_UPLOAD_TOKEN = 'uploadToken';
	const CACHE_TYPE_REPORTS_COUNT = "reportsCount";
	const CACHE_TYPE_GEO_COORDINATES = 'geoCoordinates';
	const CACHE_TYPE_ELASTIC_STICKY_SESSIONS = 'elasticStickySessions';
	const CACHE_TYPE_AUTH_CACHED_DATA = 'authCachedData';

	protected static $caches = array();
	
	/**
	 * @param string $cacheType
	 * @return array
	 */
	public static function getCacheSectionNames($cacheType)
	{
		$cacheMap = kConf::get('mapping', 'cache');		
		if (!isset($cacheMap[$cacheType]))
			return null;
		
		$cacheSections = trim($cacheMap[$cacheType]);
		if (!$cacheSections)
			return null;
				
		return explode(',', $cacheSections);
	}

	/**
	 * @param string $cacheSection
	 * @return kBaseCacheWrapper or null on error
	 */
	public static function getCache($cacheSection)
	{
		if (array_key_exists($cacheSection, self::$caches))
		{
			return self::$caches[$cacheSection];
		}
		
		// get configuration
		$sectionConfig = kConf::get($cacheSection, 'cache', null);
		if (!$sectionConfig)
		{
			return null;
		}
		
		// create cache class
		$className = $sectionConfig['class'];
		$className = "k{$className}CacheWrapper";
		
		require_once(__DIR__ . '/../../../../../infra/cache/' . $className . '.php');
		$cache = new $className;
				
		// initialize the cache
		if (call_user_func(array($cache, 'init'), $sectionConfig) === false)
		{
			$cache = null;
		}

		self::$caches[$cacheSection] = $cache;
		return $cache;
	}
	
	/**
	 * @param string $cacheType
	 * @return kBaseCacheWrapper or null on error
	 */
	public static function getSingleLayerCache($cacheType)
	{
		$cacheSections = self::getCacheSectionNames($cacheType);
		if (!$cacheSections)
		{
			return null;
		}
		
		$cacheSection = reset($cacheSections);
		
		return self::getCache($cacheSection);
	}
}
