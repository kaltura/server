<?php
class kBroadcastUrlManager
{
	const PRIMARY_MEDIA_SERVER_INDEX = 0;
	const SECONDARY_MEDIA_SERVER_INDEX = 1;
	const DEFAULT_SUFFIX = 'default';
	
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
		$dbEntry->setPrimaryBroadcastingUrl($this->getBroadcastUrl($dbEntry,  $this->getHostname(kDataCenterMgr::getCurrentDcId(), $dbEntry->getSource()), kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX));
			
		$otherDCs = kDataCenterMgr::getAllDcs();
		if(count($otherDCs))
		{
			$otherDc = reset($otherDCs);
			$otherDcId = $otherDc['id'];
			$dbEntry->setSecondaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, $this->getHostname($otherDcId, $dbEntry->getSource()), kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX));
		}
	}
	
	protected function getPostfixValue ($sourceType)
	{
		//We want the behavior to be as before.
		if (in_array($sourceType, array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_CHANNEL)))
			return self::DEFAULT_SUFFIX;
			
		$reflector = new ReflectionClass("EntrySourceType");
		KalturaLog::debug(print_r($reflector, true));
		$constantNames = array_flip($reflector->getConstants());
		
		return $constantNames[$sourceType];
	}
	
	protected function getHostname ($dc, $sourceType)
	{
		$applicationSuffix = $this->getPostfixValue($sourceType);
		$mediaServerConfig = kConf::get($dc, 'broadcast');
		$url = $mediaServerConfig['domain'];
		
		if (isset ($mediaServerConfig['application'][$applicationSuffix]))
			$app = $mediaServerConfig['application'][$applicationSuffix];
		else
		{
			KalturaLog::err("The value for $applicationSuffix does not exist in the broadcast map.");
			throw new kCoreException("The value for $applicationSuffix does not exist in the broadcast map.");
		}
		
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