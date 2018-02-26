<?php
class kBroadcastUrlManager
{
	const PRIMARY_MEDIA_SERVER_INDEX = 0;
	const SECONDARY_MEDIA_SERVER_INDEX = 1;
	
	const DEFAULT_SUFFIX = 'default';
	const DEFAULT_PORT_RTMP = 1935;
	const DEFAULT_PORT_RTSP = 554;
	
	const PROTOCOL_RTMP = 'rtmp';
	const PROTOCOL_RTSP = 'rtsp';

	const RTMP_DOMAIN = 'domain';
	const RTMP_PORT = 'port';

	const RTSP_DOMAIN = 'rtsp_domain';
	const RTSP_PORT = 'rtsp_port';

	const PROTOCOL_RTC = 'rtc';

	
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
		//if we have broadcast urls on the custom data and we regenerate token - need to save the new url
		if($dbEntry->getFromCustomData('primaryBroadcastingUrl'))
			$dbEntry->setPrimaryBroadcastingUrl($this->getPrimaryBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP));
		if($dbEntry->getFromCustomData('primaryRtspBroadcastingUrl'))
			$dbEntry->setPrimaryRtspBroadcastingUrl($this->getPrimaryBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP));
		if($dbEntry->getFromCustomData('secondaryBroadcastingUrl'))
			$dbEntry->setSecondaryBroadcastingUrl($this->getSecondaryBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTMP));
		if($dbEntry->getFromCustomData('secondaryRtspBroadcastingUrl'))
			$dbEntry->setSecondaryRtspBroadcastingUrl($this->getSecondaryBroadcastUrl($dbEntry, kBroadcastUrlManager::PROTOCOL_RTSP));
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
	
	protected function getHostname ($dc, $primary, $entry, $protocol)
	{
		$sourceType = $entry->getSource();
		$applicationSuffix = $this->getPostfixValue($sourceType);
		$broadcastConfig = $this->getConfiguration($dc);
		list($domainParam, $portParam) = self::getUrlParamsByProtocol($protocol);
		$url = $broadcastConfig[$domainParam];
		$url = str_replace(array("{entryId}", "{primary}"), array($entry->getId(), $primary ? "p" : "b"), $url);
		$port = $this->getPort($dc, $portParam, $protocol);
		
		if (isset ($broadcastConfig['application'][$applicationSuffix]))
			$app = $broadcastConfig['application'][$applicationSuffix];
		else
		{
			//return empty url
			KalturaLog::log("The value for $applicationSuffix does not exist in the broadcast map.");
			return null;
		}
		
		return "$url:$port/$app";
	}
	
	protected function getPort($dc, $portParam, $protocol)
	{
		$port = kBroadcastUrlManager::DEFAULT_PORT_RTMP;
		if($protocol == kBroadcastUrlManager::PROTOCOL_RTSP)
			$port = kBroadcastUrlManager::DEFAULT_PORT_RTSP;
	
		$broadcastConfig = $this->getConfiguration();	
		if(isset($broadcastConfig[$portParam]))
		{
			$port = $broadcastConfig[$portParam];
		}
		
		if (isset($broadcastConfig[$dc]) && isset($broadcastConfig[$dc][$portParam]))
		{
			$port = $broadcastConfig[$dc][$portParam];
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

	public static function getUrlParamsByProtocol($protocol)
	{
		if($protocol == kBroadcastUrlManager::PROTOCOL_RTMP)
			return array(kBroadcastUrlManager::RTMP_DOMAIN, kBroadcastUrlManager::RTMP_PORT);
		if($protocol == kBroadcastUrlManager::PROTOCOL_RTSP)
			return array(kBroadcastUrlManager::RTSP_DOMAIN, kBroadcastUrlManager::RTSP_PORT);
	}

	public function getPrimaryBroadcastUrl(LiveStreamEntry $entry, $protocol)
	{
		$currentDc = kDataCenterMgr::getCurrentDcId();
		$concatStreamName = ($protocol == kBroadcastUrlManager::PROTOCOL_RTSP);
		$hostname = $this->getHostName($currentDc, true, $entry, $protocol);
		return $this->getBroadcastUrl($entry, $protocol, $hostname, kBroadcastUrlManager::PRIMARY_MEDIA_SERVER_INDEX, $concatStreamName);
	}

	public function getSecondaryBroadcastUrl(LiveStreamEntry $entry, $protocol)
	{
		$currentDc = kDataCenterMgr::getCurrentDcId();
		$configuration = $this->getConfiguration();
		$concatStreamName = ($protocol == kBroadcastUrlManager::PROTOCOL_RTSP);
		foreach($configuration as $dc => $config)
		{
			if(!is_numeric($dc) || $dc == $currentDc)
				continue;

			$hostname = $this->getHostName($dc, false, $entry, $protocol);
			return $this->getBroadcastUrl($entry, $protocol, $hostname, kBroadcastUrlManager::SECONDARY_MEDIA_SERVER_INDEX, $concatStreamName);
		}
	}

	public function getRTCBroadcastingUrl(LiveStreamEntry $entry, $protocol, $hostname)
	{
		return $this->getBroadcastUrl($entry, $protocol, $hostname, -1, true);
	}
}
