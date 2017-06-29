<?php

/**
 * @package server-infra
 * @subpackage cache
 */
class kLoggerCache
{
	const LOGGER_APC_CACHE_KEY_PREFIX = 'LoggerInstance_';

	/**
	 * @param $configName string
	 * @param $context string
	 */
	static public function InitLogger($configName, $context = null)
	{
		if (KalturaLog::getLogger())	// already initialized
			return;
		
		$cache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_APC_LCAL);
		if($cache)
		{
			$cacheKey = self::LOGGER_APC_CACHE_KEY_PREFIX . $configName;
			$logger = $cache->get($cacheKey);
			if ($logger)
			{
				list($logger, $cacheVersionId) = $logger;
				if ($cacheVersionId == kConf::getCachedVersionId())
				{
					KalturaLog::setLogger($logger);
					return;
				}
			}
		}

		try // we don't want to fail when logger is not configured right
		{
			$config = new Zend_Config(kConf::getMap('logger'));
			
			KalturaLog::initLog($config->$configName);
			if ($context)
				KalturaLog::setContext($context);
			
			if($cache)
				$cache->set($cacheKey, array(KalturaLog::getLogger(), kConf::getCachedVersionId()));
		}
		catch(Zend_Config_Exception $ex)
		{
		}
	}
}
