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
	const APIV3_FAIL_PING = "APIV3_FAIL_PING";
	
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
		if(function_exists('apc_fetch') && apc_fetch(self::APIV3_FAIL_PING))
			return false;
		
		if(function_exists('apcu_fetch') && apcu_fetch(self::APIV3_FAIL_PING))
			return false;
		
		return true;
	}
	
	/**
	 * @action pingDatabase
	 * @return bool Always true if database available and writeable
	 * @ksIgnored
	 */
	function pingDatabaseAction()
	{
		$hostname = infraRequestUtils::getHostname();
		$server = ApiServerPeer::retrieveByHostname($hostname);
		if(!$server)
		{
			$server = new ApiServer();
			$server->setHostname($hostname);
		}
		
		$server->setUpdatedAt(time());
		if(!$server->save())
			return false;
			
		return true;
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
		$version = file_get_contents(realpath(dirname(__FILE__)) . '/../../VERSION.txt');
		return trim($version);
	}
}
