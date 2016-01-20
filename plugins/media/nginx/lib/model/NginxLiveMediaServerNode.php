<?php

class NginxLiveMediaServerNode extends MediaServerNode {
	const DEFAULT_MANIFEST_PORT = 1935;
	const CUSTOM_DATA_APP_PREFIX = 'app_prefix';
	
	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		
		$this->setType(NginxLivePlugin::getNginxLiveMediaServerTypeCoreValue(NginxLiveMediaServerNodeType::NGINX_LIVE_MEDIA_SERVER));
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
		return null;
	}
	
	public function getPlaybackHost($protocol = 'http', $format = null, $deliveryType = null)
	{
		$mediaServerGlobalConfig = array();
		
		if(kConf::hasMap('media_servers'))
			$mediaServerGlobalConfig = array_merge($mediaServerGlobalConfig, kConf::getMap('media_servers'));
		
		if($this->partner_media_server_config)
			$mediaServerGlobalConfig = array_merge($mediaServerGlobalConfig, $this->partner_media_server_config);
			
		$domain = $this->getDomainByProtocolAndFormat($mediaServerGlobalConfig, $protocol, $format);
		
		$port = $this->getPortByProtocolAndFormat($mediaServerGlobalConfig, $protocol, $format);
		
		$appPrefix = $this->getApplicationPrefix($mediaServerGlobalConfig);
		
		return "$domain:$port/$appPrefix";
	}
	
	/**
	 * @param string $serviceName
	 * @return KalturaMediaServerClient
	 */
	public function getWebService($serviceName)
	{	
		return null;
	}
	
	public function getDomainByProtocolAndFormat($mediaServerConfig, $protocol = 'http', $format = null)
	{	
		$domain = $this->getPlaybackDomain();
		
		$domainField = "domain" . ($format ? "-$format" : "");
		
		$domain = $this->getValueByField($mediaServerConfig, $domainField, $domain);
		
		$mediaServerPlaybackDomainConfig = $this->getMediaServerPlaybackDomainConfig();
		if($mediaServerPlaybackDomainConfig)
		{
			$domainField = $protocol . ($format ? "-$format" : "");
			if(isset($mediaServerPlaybackDomainConfig[$domainField]))
				$domain = $mediaServerPlaybackDomainConfig[$domainField];
		}
		
		return $domain;
	}
	
	public function getPortByProtocolAndFormat($mediaServerConfig, $protocol = 'http', $format = null)
	{
		$port = NginxLiveMediaServerNode::DEFAULT_MANIFEST_PORT;
		
		$portField = 'port' . ($protocol != 'http' ? "-$protocol" : "") . ($format ? "-$format" : "");
		
		$port = $this->getValueByField($mediaServerConfig, $portField, $port);
		
		$mediaServerPortConfig = $this->getMediaServerPortConfig();
		if($mediaServerPortConfig)
		{
			$portField = $protocol . ($format ? "-$format" : "");
			if(isset($mediaServerPortConfig[$portField]) && $mediaServerPortConfig[$portField] !== NginxLiveMediaServerNode::DEFAULT_MANIFEST_PORT)
				$port = $mediaServerPortConfig[$portField];
		}
		
		return $port;
	}
	
	public function getApplicationPrefix($mediaServerConfig)
	{
		$appPrefix = "";

		$appPrefix = $this->getValueByField($mediaServerConfig, 'appPrefix', $appPrefix);
		
		if($this->getAppPrefix())
			$appPrefix = $this->getApplicationPrefix();
		
		return $appPrefix;
	}
	
	public function getValueByField($config, $filedValue, $defaultValue)
	{
		$value = $defaultValue;
		
		if(isset($config[$filedValue]))
			$value = $config[$filedValue];
		if(isset($config['dc-'.$this->getDc()][$filedValue]))
			$value = $config['dc-'.$this->getDc()][$filedValue];
		if(isset($config[$this->getHostname()][$filedValue]))
			$value = $config[$this->getHostname()][$filedValue];
		
		return $value;
	}
	
	public function setAppPrefix($appPrefix)
	{
		$this->putInCustomData(self::CUSTOM_DATA_APP_PREFIX, $appPrefix);
	}
	
	public function getAppPrefix()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_APP_PREFIX, null, null);
	}

} // NginxLiveMediaServer
