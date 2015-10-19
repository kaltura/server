<?php

class WowzaMediaServerNode extends MediaServerNode {
	const DEFAULT_MANIFEST_PORT = 1935;
	const DEFAULT_WEB_SERVICES_PORT = 888;
	const DEFAULT_WEB_SERVICES_PROTOCOL = 'http';
	const DEFAULT_TRANSCODER = 'default';
	const DEFAULT_GPUID = -1;
	
	const CUSTOM_DATA_APP_PREFIX = 'app_prefix';
	const CUSTOM_DATA_TRANSCODER_CONFIG = 'transcoder';
	const CUSTOM_DATA_GPUID = 'gpuid';
	const CUSTOM_DATA_LIVE_SERVICE_PORT = 'live_service_port';
	const CUSTOM_DATA_LIVE_SERVICE_PROTOCOL = 'live_service_protocol';
	const CUSTOM_DATA_LIVE_SERVICE_INTERNAL_DOMAIN = 'live_service_internal_domain';
	
	const WEB_SERVICE_LIVE = 'live';
	
	static protected $webServices = array(
		self::WEB_SERVICE_LIVE => 'KalturaMediaServerLiveService',
	);
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		
		$this->setType(WowzaPlugin::getWowzaMediaServerTypeCoreValue(WowzaMediaServerNodeType::WOWZA_MEDIA_SERVER));
	}
	
	public function getManifestUrl($protocol = 'http', $format = null)
	{		
		$playbackHost = $this->getPlaybackHost($protocol, $format);
		
		$hostname = $this->getHostname();
		if(!$this->getIsExternalMediaServer())
			$hostname = preg_replace('/\..*$/', '', $hostname);
		
		$url = "$protocol://$playbackHost";
		$url = str_replace("{hostName}", $hostname, $url);
		return $url;
		
	}
	
	public function getLiveWebServiceName()
	{
		return WowzaMediaServerNode::WEB_SERVICE_LIVE;
	}
	
	public function getPlaybackHost($protocol = 'http', $format = null)
	{	
		$domain = $this->getDomainByProtocolAndFormat($protocol, $format);
		
		$port = $this->getPortByProtocolAndFormat($protocol, $format);
		
		$appPrefix = $this->getAppPrefix();
		
		return "$domain:$port/$appPrefix";
	}
	
	/**
	 * @param string $serviceName
	 * @return KalturaMediaServerClient
	 */
	public function getWebService($serviceName)
	{	
		if(!isset(self::$webServices[$service]))
			return null;
			
		$serviceClass = self::$webServices[$service];
		
		$domain = $this->getLiveServiceInternalDomain() ? $this->getLiveServiceInternalDomain() : $this->getHostname();
		$port = $this->getLiveServicePort();
		$protocol = $this->getLiveServiceProtocol();
		
		$url = "$protocol://$domain:$port/$service?wsdl";
		KalturaLog::debug("Service URL: $url");
		return new $serviceClass($url);
	}
	
	public function getDomainByProtocolAndFormat($protocol = 'http', $format = null)
	{	
		$domain = $this->getPlaybackHostName();
		
		$domainField = $protocol;
		if($format)
			$domainField .= "-$format";
		
		$mediaServerPlaybackDomainConfig = $this->getMediaServerPlaybackDomainConfig();
		if($mediaServerPlaybackDomainConfig && isset($mediaServerPlaybackDomainConfig[$domainField]) && $mediaServerPlaybackDomainConfig[$domainField] !== $domain)
			$domain = $mediaServerPlaybackDomainConfig[$domainField];
		
		if(!$this->partner_media_server_config)
			return $domain;

		$domainField = "domain-" . $domainField; 
		if(isset($this->partner_media_server_config[$domainField]))
			$domain = $this->partner_media_server_config[$domainField];
		if(isset($this->partner_media_server_config['dc-'.$this->getDc()][$domainField]))
			$domain = $this->partner_media_server_config['dc-'.$this->getDc()][$domainField];
		if(isset($this->partner_media_server_config[$this->getHostname()][$domainField]))
			$domain = $this->partner_media_server_config[$this->getHostname()][$domainField];
		
		return $domain;
	}
	
	public function getPortByProtocolAndFormat($protocol = 'http', $format = null)
	{
		$port = WowzaMediaServerNode::DEFAULT_MANIFEST_PORT;
		
		$portField = $protocol;
		if($format)
			$portField .= "-$format";
		
		$mediaServerPortConfig = $this->getMediaServerPortConfig();
		if($mediaServerPortConfig && isset($mediaServerPortConfig[$portField]) && $mediaServerPortConfig[$portField] !== WowzaMediaServerNode::DEFAULT_MANIFEST_PORT)
			$port = $mediaServerPortConfig[$portField];
		
		if(!$this->partner_media_server_config)
			return $port;
		
		$portField = "port-" . $portField;
		if(isset($this->partner_media_server_config[$portField]))
			$port = $this->partner_media_server_config[$portField];
		if(isset($this->partner_media_server_config['dc-'.$this->getDc()][$portField]))
			$port = $this->partner_media_server_config['dc-'.$this->getDc()][$portField];
		if(isset($this->partner_media_server_config[$this->getHostname()][$portField]))
			$port = $this->partner_media_server_config[$this->getHostname()][$portField];
		
		return $port;
	}
	
	public function setAppPrefix($appPrefix)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APP_PREFIX, $appPrefix);
	}
	
	public function getAppPrefix()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APP_PREFIX, null, "");
	}
	
	public function setTranscoder($transcoder)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TRANSCODER_CONFIG, $transcoder);
	}
	
	public function getTranscoder()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TRANSCODER_CONFIG, null, WowzaMediaServerNode::DEFAULT_TRANSCODER);
	}
	
	public function setGPUID($gpuid)
	{
		$this->putInCustomData(self::CUSTOM_DATA_GPUID, $gpuid);
	}
	
	public function getGPUID()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_GPUID, null, WowzaMediaServerNode::DEFAULT_GPUID);
	}
	
	public function setLiveServicePort($liveServicePort)
	{
		$this->putInCustomData(self::CUSTOM_DATA_LIVE_SERVICE_PORT, $liveServicePort);
	}
	
	public function getLiveServicePort()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LIVE_SERVICE_PORT, null, WowzaMediaServerNode::DEFAULT_WEB_SERVICES_PORT);
	}
	
	public function setLiveServiceProtocol($liveServiceProtocol)
	{
		$this->putInCustomData(self::CUSTOM_DATA_LIVE_SERVICE_PROTOCOL, $liveServiceProtocol);
	}
	
	public function getLiveServiceProtocol()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LIVE_SERVICE_PROTOCOL, null, WowzaMediaServerNode::DEFAULT_WEB_SERVICES_PROTOCOL);
	}
	
	public function setLiveServiceInternalDomain($liveServiceInternalDomain)
	{
		$this->putInCustomData(self::CUSTOM_DATA_LIVE_SERVICE_INTERNAL_DOMAIN, $liveServiceInternalDomain);
	}
	
	public function getLiveServiceInternalDomain()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_LIVE_SERVICE_INTERNAL_DOMAIN, null, null);
	}

} // WowzaMediaServer
