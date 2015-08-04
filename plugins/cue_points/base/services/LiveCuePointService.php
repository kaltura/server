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
		if (!$this->getKs() || !$this->getKs()->isAdmin()) {
			KalturaCriterion::enableTag(KalturaCriterion::TAG_USER_SESSION);
			CuePointPeer::setUserContentOnly(true);
		}
		
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
	 * @deprecated This actions is not required, sync points are sent automatically on the stream.
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
	}
}
