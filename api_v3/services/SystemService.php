<?php

/**
 * System service is used for internal system helpers & to retrieve system level information
 *
 * @service system
 * @package api
 * @subpackage services
 */
class SystemService extends KalturaBaseService
{
	
	protected function partnerRequired($actionName)
	{
		if ($actionName == 'ping' || $actionName == 'getTime') {
			return false;
		}
		return parent::partnerRequired($actionName);
	}
	
	/**
	 * @action ping
	 * @return bool Always true if service is working
	 * @ksIgnored
	 */
	function pingAction()
	{
		$expiry = kConf::get('ping_cache_expiry_time', 'runtime_config', 5);
		kApiCache::setConditionalCacheExpiry($expiry);
		kApiCache::setExpiry($expiry);
		return mySystemUtils::ping();
	}
	
	/**
	 * @action pingDatabase
	 * @return bool Always true if database available and writeable
	 * @ksIgnored
	 */
	function pingDatabaseAction()
	{
		return mySystemUtils::pingMySql();
	}
	
	/**
	 *
	 *
	 * @action getTime
	 * @return int Return current server timestamp
	 * @ksIgnored
	 */
	function getTimeAction()
	{
		KalturaResponseCacher::disableCache();
		return time();
	}
	
	/**
	 * @action getVersion
	 * @return string the current server version
	 * @ksIgnored
	 */
	function getVersionAction()
	{	
		KalturaResponseCacher::disableCache();
		return mySystemUtils::getVersion();
	}


	/**
	 * @action getHealthCheck
	 * @return string the server healthCheck info
	 * @ksIgnored
	 */
	function getHealthCheckAction()
	{
		KalturaResponseCacher::disableCache();
		list($healthCheckInfo, $notifyError) = mySystemUtils::getHealthCheckInfo();
		return new kRendererString($healthCheckInfo, 'text/plain', 8640000, null, $notifyError);
	}
}
