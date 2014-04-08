<?php
/**
 * Live Cue Point service
 *
 * @service liveCuePoint
 * @package plugins.cuePoint
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */
class LiveCuePointService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('CuePoint');

		// when session is not admin, allow access to user entries only
		if (!$this->getKs() || !$this->getKs()->isAdmin())
			CuePointPeer::setDefaultCriteriaFilterByKuser();
		
		if(!CuePointPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, CuePointPlugin::PLUGIN_NAME);
		
		if(!$this->getPartner()->getEnabledService(PermissionName::FEATURE_KALTURA_LIVE_STREAM))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, 'Kaltura Live Streams');
	}

	/**
	 * Creates perioding metadata sync-point events on a live stream
	 * 
	 * @action createPeriodicSyncPoints
	 * @actionAlias liveStream.createPeriodicSyncPoints
	 * @param string $entryId Kaltura live-stream entry id
	 * @param int $interval Events interval in seconds 
	 * @param int $duration Duration in seconds
	 * 
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::NO_MEDIA_SERVER_FOUND
	 * @throws KalturaErrors::MEDIA_SERVER_SERVICE_NOT_FOUND
	 */
	function createPeriodicSyncPoints($entryId, $interval, $duration)
	{
		$entryDc = substr($entryId, 0, 1);
		if($entryDc != kDataCenterMgr::getCurrentDcId())
		{
			$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId($entryDc);
			kFileUtils::dumpApiRequest($remoteDCHost);
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry || $dbEntry->getType() != KalturaEntryType::LIVE_STREAM || $dbEntry->getSource() != KalturaSourceType::LIVE_STREAM)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		/* @var $dbEntry LiveStreamEntry */
		$kMediaServer = $dbEntry->getMediaServer(true);
		if(!$kMediaServer)
			throw new KalturaAPIException(KalturaErrors::NO_MEDIA_SERVER_FOUND, $entryId);
			
		$mediaServer = $dbEntry->getMediaServer();
		if(!$mediaServer)
			throw new KalturaAPIException(KalturaErrors::NO_MEDIA_SERVER_FOUND, $entryId);
		
		$mediaServerCuePointsService = $mediaServer->getWebService(MediaServer::WEB_SERVICE_CUE_POINTS);
		if($mediaServerCuePointsService && $mediaServerCuePointsService instanceof KalturaMediaServerCuePointsService)
		{
			$mediaServerCuePointsService->createTimeCuePoints($entryId, $interval, $duration);
		}
		else 
		{
			throw new KalturaAPIException(KalturaErrors::MEDIA_SERVER_SERVICE_NOT_FOUND, $mediaServer->getId(), MediaServer::WEB_SERVICE_LIVE);
		}
	}
}
