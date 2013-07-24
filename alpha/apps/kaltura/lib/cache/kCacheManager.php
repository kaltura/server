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
