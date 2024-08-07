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
		
		if (kApcWrapper::functionExists('fetch'))
		{
			$cacheKey = self::LOGGER_APC_CACHE_KEY_PREFIX . $configName;
			$logger = kApcWrapper::apcFetch($cacheKey);
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
					
			if (kApcWrapper::functionExists('store'))
				kApcWrapper::apcStore($cacheKey, array(KalturaLog::getLogger(), kConf::getCachedVersionId()));
		}
		catch(Zend_Config_Exception $ex)
		{
		}
	}
}
