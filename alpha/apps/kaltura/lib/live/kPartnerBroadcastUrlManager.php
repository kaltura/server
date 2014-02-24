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
		
		$dbEntry->setPrimaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, $partner->getPrimaryBroadcastUrl(), self::PRIMARY_MEDIA_SERVER_INDEX));
		$dbEntry->setSecondaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, $partner->getSecondaryBroadcastUrl(), self::SECONDARY_MEDIA_SERVER_INDEX));
	}
	
}