<?php

/**
 * Manage media servers
 *
 * @service mediaServer
 */
class MediaServerService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('MediaServer'); 	
	}
	
	/**
	 * Get media server by hostname
	 * 
	 * @action get
	 * @param string $hostname
	 * @return KalturaMediaServer
	 * 
	 * @throws KalturaErrors::ACCESS_CONTROL_ID_NOT_FOUND
	 */
	function getAction($hostname)
	{
		$dbMediaServer = MediaServerPeer::retrieveByHostname($hostname);
		if (!$dbMediaServer)
			throw new KalturaAPIException(KalturaErrors::MEDIA_SERVER_NOT_FOUND, $hostname);
			
		$mediaServer = new KalturaMediaServer();
		$mediaServer->fromObject($dbMediaServer);
		return $mediaServer;
	}
	
	/**
	 * Update media server status
	 * 
	 * @action reportStatus
	 * @param string $hostname
	 * @param KalturaMediaServerStatus $mediaServerStatus
	 * @return KalturaMediaServer
	 */
	function reportStatusAction($hostname, KalturaMediaServerStatus $mediaServerStatus)
	{
		$dbMediaServer = MediaServerPeer::retrieveByHostname($hostname);
		if (!$dbMediaServer)
		{
			$dbMediaServer = new MediaServer();
			$dbMediaServer->setHostname($hostname);
			$dbMediaServer->setDc(kDataCenterMgr::getCurrentDcId());
		}
		
		$mediaServerStatus->toUpdatableObject($dbMediaServer);
		$dbMediaServer->save();
		
		$mediaServer = new KalturaMediaServer();
		$mediaServer->fromObject($dbMediaServer);
		return $mediaServer;
	}
}