<?php
class kMultiCentersBroadcastUrlManager extends kBroadcastUrlManager
{
	public function getBroadcastUrl(entry $entry, $mediaServerIndex)
	{
		$mediaServerConfig = kConf::get($mediaServerIndex, 'broadcast');
		$app = $mediaServerConfig['application'];
		
		$partnerId = $this->partnerId;
		$url = "rtmp://$partnerId.$mediaServerIndex." . kConf::get('domain', 'broadcast');
		$entryId = $entry->getId();
		$token = $entry->getStreamPassword();
		return "$url/$app/p/$partnerId/e/$entryId/i/$mediaServerIndex/t/$token"; 
	}
}