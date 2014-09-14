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
	 */
	function pingAction()
	{
		return true;
	}
	
	/**
	 * @action pingDatabase
	 * @return bool Always true if database available and writeable
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
	 */
	function getTimeAction()
	{
		KalturaResponseCacher::disableCache();
		return time();
	}
	
	/**
	 * @action getVersion
	 * @return string the current server version
	 */
	function getVersionAction()
	{	
		$version = file_get_contents(realpath(dirname(__FILE__)) . '/../../VERSION.txt');
		return trim($version);
	}
}
