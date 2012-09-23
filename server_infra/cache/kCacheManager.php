<?php

/**
 * @package infra
 * @subpackage cache
 */
class kCacheManager
{
	// Cache ids
	const APC = 1;
	
	const MC_LOCAL = 11;
	const MC_GLOBAL_KEYS = 12;
	const MC_GLOBAL_QUERIES = 13;
	const MC_GLOBAL_FILESYNC = 14;
	
	const FS_API_V3 = 21;
	const FS_API_V3_FEED = 22;
	const FS_PLAY_MANIFEST = 23;
	const FS_ENTRY = 24;
	
	const COMPRESSED = 1;
	
	const KCONF_PARAM_PREFIX = 'kConf:';
	
	protected static $caches = array();
	
	protected static $cacheConfigs = array(
		self::APC =>				array('Apc'),
		
		self::MC_LOCAL => 			array('Memcache',	'kConf:memcache_host', 					'kConf:memcache_port',					0),
		self::MC_GLOBAL_KEYS => 	array('Memcache',	'kConf:global_keys_memcache_host', 		'kConf:global_keys_memcache_port',		0),
		self::MC_GLOBAL_QUERIES => 	array('Memcache',	'kConf:global_queries_memcache_host', 	'kConf:global_queries_memcache_port',	self::COMPRESSED),
		self::MC_GLOBAL_FILESYNC => array('Memcache',	'kConf:global_filesync_memcache_host', 	'kConf:global_filesync_memcache_port',	self::COMPRESSED),
		
		self::FS_API_V3 => 			array('FileSystem',	'kConf:response_cache_dir', 'cache_v3-600', 		2, false, 600,	 	false),
		self::FS_API_V3_FEED => 	array('FileSystem',	'kConf:global_cache_dir', 	'feed/cache_v3-86400', 	2, false, 86400,	false),
		self::FS_PLAY_MANIFEST => 	array('FileSystem',	'kConf:response_cache_dir', 'cache_manifest', 		2, false,  600,		false),
		self::FS_ENTRY => 			array('FileSystem',	'kConf:global_cache_dir', 	'entry', 				4, false, 0,		false),
	);
	
	/**
	 * @param int $type
	 * @return kBaseCacheWrapper or null on error
	 */
	public static function getCache($type)
	{
		if (array_key_exists($type, self::$caches))
		{
			return self::$caches[$type];
		}
		
		if (!array_key_exists($type, self::$cacheConfigs))
		{
			return null;
		}
		
		$config = self::$cacheConfigs[$type];
		$className = "k{$config[0]}CacheWrapper";

		require_once(__DIR__ . '/../../infra/cache/' . $className . '.php');
		$cache = new $className;

		// get required kConf params
		$config = array_slice($config, 1);
		foreach ($config as $index => $value)
		{
			if (is_string($value) && substr($value, 0, strlen(self::KCONF_PARAM_PREFIX)) == self::KCONF_PARAM_PREFIX)
			{
				$value = substr($value, strlen(self::KCONF_PARAM_PREFIX));
				if (!kConf::hasParam($value))
				{
					self::$caches[$type] = null;
					return null;
				}
				
				$config[$index] = kConf::get($value);
			}
		}
		
		// initialize the cache
		if (call_user_func_array(array($cache, 'init'), $config) === false)
		{
			$cache = null;
		}

		self::$caches[$type] = $cache;
		return $cache;
	}
}
