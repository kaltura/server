<?php
class kBroadcastUrlManager
{
	const PRIMARY_MEDIA_SERVER_INDEX = 0;
	const SECONDARY_MEDIA_SERVER_INDEX = 1;
	
	protected $partnerId;
	
	protected function __construct($partnerId)
	{
		$this->partnerId = $partnerId;
	}
	
	public static function getInstance($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if($partner->getBroadcastUrlManager())
		{
			$broadcastUrlManager = $partner->getBroadcastUrlManager();
			if(class_exists($broadcastUrlManager))
				return new $broadcastUrlManager($partnerId);
		}
	
		if(kConf::hasParam('broadcast_url_manager'))
		{
			$broadcastUrlManager = kConf::get('broadcast_url_manager');
			if(class_exists($broadcastUrlManager))
				return new $broadcastUrlManager($partnerId);
		}
		
		return new kBroadcastUrlManager($partnerId);
	}
	
	public function getBroadcastUrl(entry $entry, $dc, $mediaServerIndex)
	{
		$mediaServerConfig = kConf::get($dc, 'broadcast');
		$url = 'rtmp://' . $mediaServerConfig['domain'];
		$app = $mediaServerConfig['application'];
		$partnerId = $this->partnerId;
		$entryId = $entry->getId();
		$token = $entry->getStreamPassword();
		return "$url/$app/p/$partnerId/e/$entryId/i/$mediaServerIndex/t/$token"; 
	}
}