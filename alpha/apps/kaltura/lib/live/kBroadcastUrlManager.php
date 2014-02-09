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
	
	public function setEntryBroadcastingUrls (LiveStreamEntry $dbEntry)
	{
		$dbEntry->setPrimaryBroadcastingUrl($this->getBroadcastUrl($dbEntry,  $this->getHostname(kDataCenterMgr::getCurrentDcId()), kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX));
			
		$otherDCs = kDataCenterMgr::getAllDcs();
		if(count($otherDCs))
		{
			$otherDc = reset($otherDCs);
			$otherDcId = $otherDc['id'];
			$dbEntry->setSecondaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, $this->getHostname($otherDcId), kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX));
		}
	}
	
	protected function getHostname ($dc)
	{
		$mediaServerConfig = kConf::get($dc, 'broadcast');
		$url = 'rtmp://' . $mediaServerConfig['domain'];
		$app = $mediaServerConfig['application'];
		
		return "$url/$app";
	}
	
	protected function getBroadcastUrl(LiveStreamEntry $entry, $hostname, $mediaServerIndex)
	{
		if (!$hostname)
		{
			return '';
		}
		
		$url = 'rtmp://' . $hostname;
		
		$params = array(
			'p' => $this->partnerId,
			'e' => $entry->getId(),
			'i' => $mediaServerIndex,
			't' => $entry->getStreamPassword(),
		);
		$paramsStr = http_build_query($params);
		
		return "$url/?$paramsStr"; 
	}
	
}