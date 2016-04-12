<?php
class kBroadcastUrlManager
{
	const PRIMARY_MEDIA_SERVER_INDEX = 0;
	const SECONDARY_MEDIA_SERVER_INDEX = 1;
	
	const DEFAULT_SUFFIX = 'default';
	const DEFAULT_PORT = 1935;
	
	const PROTOCOL_RTMP = 'rtmp';
	const PROTOCOL_RTSP = 'rtsp';
	
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
		$currentDc = kDataCenterMgr::getCurrentDcId();
		$hostname = $this->getHostname($currentDc, true, $dbEntry);
		
		$dbEntry->setPrimaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP, $hostname, kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX));		
		$dbEntry->setPrimaryRtspBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP, $hostname, kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX, true));
			
		$configuration = $this->getConfiguration();
		foreach($configuration as $dc => $config)
		{
			if(!is_numeric($dc) || $dc == $currentDc)
				continue;
				
			$hostname = $this->getHostname($dc, false, $dbEntry);
			
			$dbEntry->setSecondaryBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP, $hostname, kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX));
			$dbEntry->setSecondaryRtspBroadcastingUrl($this->getBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP, $hostname, kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX, true));
		}
	}
	
	protected function getPostfixValue ($sourceType)
	{
		//We want the behavior to be as before.
		if (in_array($sourceType, array(EntrySourceType::LIVE_STREAM, EntrySourceType::LIVE_CHANNEL)))
			return self::DEFAULT_SUFFIX;
			
		$reflector = new ReflectionClass("EntrySourceType");
		$constantNames = array_flip($reflector->getConstants());
		
		return $constantNames[$sourceType];
	}
	
	protected function getConfiguration ($dc = null)
	{
		$partner = PartnerPeer::retrieveByPK($this->partnerId);
		return $partner->getLiveStreamBroadcastUrlConfigurations($dc);
	}
	
	protected function getHostname ($dc, $primary, $entry)
	{
		$sourceType = $entry->getSource();
		$applicationSuffix = $this->getPostfixValue($sourceType);
		$mediaServerConfig = $this->getConfiguration($dc);
		$url = $mediaServerConfig['domain'];
		$url = str_replace(array("{entryId}", "{primary}"), array($entry->getId(), $primary ? "p" : "b"), $url);
		$port = $this->getPort($dc);
		
		if (isset ($mediaServerConfig['application'][$applicationSuffix]))
			$app = $mediaServerConfig['application'][$applicationSuffix];
		else
		{
			KalturaLog::err("The value for $applicationSuffix does not exist in the broadcast map.");
			throw new kCoreException("The value for $applicationSuffix does not exist in the broadcast map.");
		}
		
		return "$url:$port/$app";
	}
	
	protected function getPort ($dc)
	{
		$port = kBroadcastUrlManager::DEFAULT_PORT;
	
		$broadcastConfig = $this->getConfiguration();	
		if(isset($broadcastConfig['port']))
		{
			$port = $broadcastConfig['port'];
		}
		
		if (isset($broadcastConfig[$dc]) && isset($broadcastConfig[$dc]['port']))
		{
			$port = $broadcastConfig[$dc]['port'];
		}
		
		return $port;
	}
	
	protected function getBroadcastUrl(LiveStreamEntry $entry, $protocol, $hostname, $mediaServerIndex, $concatStreamName = false)
	{
		if (!$hostname)
		{
			return '';
		}
		
		$url = "$protocol://$hostname";
		
		$params = array(
			'p' => $this->partnerId,
			'e' => $entry->getId(),
			'i' => $mediaServerIndex,
			't' => $entry->getStreamPassword(),
		);
		$paramsStr = http_build_query($params);
		
		$streamName = '';
		if($concatStreamName)
		{
			$streamName = $entry->getId() . '_%i';
		}
		
		return "$url/$streamName?$paramsStr"; 
	}
	
}
