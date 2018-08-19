<?php
class kPartnerBroadcastUrlManager extends kBroadcastUrlManager
{
	protected function getHostName($dc, $primary, $entry, $protocol)
	{
		$partner  = PartnerPeer::retrieveByPK($this->partnerId);
		if (!$partner)
		{
			KalturaLog::info("Partner with id [{$this->partnerId}] was not found");
			return null;
		}

		if($primary)
			return $partner->getPrimaryBroadcastUrl();

		return $partner->getSecondaryBroadcastUrl();
	}

}