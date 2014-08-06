<?php
class kPartnerBroadcastUrlManager extends kBroadcastUrlManager
{
	public function setEntryBroadcastingUrls (LiveStreamEntry $dbEntry)
	{
		$partner  = PartnerPeer::retrieveByPK($this->partnerId);
		if (!$partner)
		{
			KalturaLog::info("Partner with id [{$this->partnerId}] was not found");
			return;
		}
		
		$hostname = $partner->getPrimaryBroadcastUrl();
		$dbEntry->setPrimaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP, $hostname, self::PRIMARY_MEDIA_SERVER_INDEX));
		$dbEntry->setPrimaryRtspBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP, $hostname, self::PRIMARY_MEDIA_SERVER_INDEX, true));
		
	
		$hostname = $partner->getSecondaryBroadcastUrl();
		$dbEntry->setSecondaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP, $hostname, self::SECONDARY_MEDIA_SERVER_INDEX));
		$dbEntry->setSecondaryRtspBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP, $hostname, self::SECONDARY_MEDIA_SERVER_INDEX, true));
	}
	
}