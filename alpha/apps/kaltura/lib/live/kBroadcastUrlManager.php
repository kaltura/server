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
	protected $useOldUrlPattern;
	
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
	
	protected function getExtraQueryParamsConfig(LiveStreamEntry $entry, $mediaServerIndex)
	{
		$extraQueryPrams = array();
		$broadcastConfig = $this->getConfiguration(kDataCenterMgr::getCurrentDcId());
		$extarQueryParamsConfig =  isset($broadcastConfig['queryParams']) ? $broadcastConfig['queryParams'] : "";
		$extarQueryParamsConfigArr = explode('.', $extarQueryParamsConfig);
		
		//Support none SaaS envioremnts
		foreach ($extarQueryParamsConfigArr as $extarQueryParamsConfig)
		{
			switch($extarQueryParamsConfig)
			{
				case "{p}":
					$extraQueryPrams['p'] = $this->partnerId;
					break;
		
				case "{i}":
					$extraQueryPrams['i'] = $mediaServerIndex;
					break;
		
				case "{e}":
					$extraQueryPrams['e'] = $entry->getId();
					break;
			}
		}
		
		return $extraQueryPrams;
	}
	
	protected function getQueryParams(LiveStreamEntry $entry, $mediaServerIndex)
	{
		$queryParams = array('t' => $entry->getStreamPassword());
		
		//Support eCDN partner using old mediaServers that must recieve additional info to operate
		if($this->useOldUrlPattern)
		{
			$queryParams = array_merge(array('p' => $this->partnerId, 'e' => $entry->getId(), 'i' => $mediaServerIndex), $queryParams);
			return http_build_query($queryParams);
		}
		
		$extraQueryPrams = $this->getExtraQueryParamsConfig($entry, $mediaServerIndex);
		$queryParams = array_merge($extraQueryPrams, $queryParams);	
		return http_build_query($queryParams);
	}
	
	protected function getBroadcastUrl(LiveStreamEntry $entry, $protocol, $hostname, $mediaServerIndex, $concatStreamName = false)
	{
		if (!$hostname)
		{
			return '';
		}
		
		if(PermissionPeer::isValidForPartner("FEATURE_HYBRID_ECDN", $entry->getPartnerId()))
			$this->useOldUrlPattern = true;
		
		$url = "$protocol://$hostname";
		$url .= $concatStreamName ? "/" . $entry->getId() . '_%i' : '';
		$paramsStr = $this->getQueryParams($entry, $mediaServerIndex);
		
		return "$url" . ($this->useOldUrlPattern ? "/" : "") . "?$paramsStr";
	}
	
}
