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
		$port = kBroadcastUrlManager::DEFAULT_PORT;
		if(strpos($hostname, ':') > 0)
		{
			list($hostname, $port) = explode(':', $hostname, 2);
		}
		$dbEntry->setPrimaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP, $hostname, self::PRIMARY_MEDIA_SERVER_INDEX, $port));
		$dbEntry->setPrimaryRtspBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP, $hostname, self::PRIMARY_MEDIA_SERVER_INDEX, $port, true));
		
	
		$hostname = $partner->getSecondaryBroadcastUrl();
		$port = kBroadcastUrlManager::DEFAULT_PORT;
		if(strpos($hostname, ':') > 0)
		{
			list($hostname, $port) = explode(':', $hostname, 2);
		}
		$dbEntry->setSecondaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP, $hostname, self::SECONDARY_MEDIA_SERVER_INDEX, $port));
		$dbEntry->setSecondaryRtspBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP, $hostname, self::SECONDARY_MEDIA_SERVER_INDEX, $port, true));
	}
	
}