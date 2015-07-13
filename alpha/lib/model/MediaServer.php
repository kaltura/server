<?php


/**
 * Skeleton subclass for representing a row from the 'media_server' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class MediaServer extends BaseMediaServer {
	const DEFAULT_MANIFEST_PORT = 1935;
	const DEFAULT_WEB_SERVICES_PORT = 888;
	const DEFAULT_APPLICATION = 'kLive';
	const DEFAULT_TRANSCODER = 'default';
	const DEFAULT_GPUID = -1;
	
	const WEB_SERVICE_LIVE = 'live';
	
	private $isExternalMediaServer = false;
	
	static protected $webServices = array(
		self::WEB_SERVICE_LIVE => 'KalturaMediaServerLiveService',
	);
	
	
	public function getTranscoder()
	{
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer['transcoder']))
					return $mediaServer['transcoder'];
			}
					
			if(isset($mediaServers['transcoder']))
				return $mediaServers['transcoder'];
		}
		
		return MediaServer::DEFAULT_TRANSCODER;
	}
	
	public function getGPUID()
	{
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer['GPUID']))
					return $mediaServer['GPUID'];
			}
					
			if(isset($mediaServers['GPUID']))
				return $mediaServers['GPUID'];
		}
		
		return MediaServer::DEFAULT_GPUID;
	}
	
	public function getManifestUrl($protocol = 'http', $partnerMediaServerConfigurations = null)
	{
		$domain = $this->getHostname();
		$port = MediaServer::DEFAULT_MANIFEST_PORT;
		$portField = 'port';
		$appPrefix = '';
		if($protocol != 'http')
			$portField .= "-$protocol";
		
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			if ($partnerMediaServerConfigurations)
				$mediaServers = array_merge($mediaServers, $partnerMediaServerConfigurations);
			
			if(isset($mediaServers[$portField]))
				$port = $mediaServers[$portField];
				
			if(isset($mediaServers['domain']))
				$domain = $mediaServers['domain'];
			elseif(isset($mediaServers['search_regex_pattern']) && isset($mediaServers['replacement']))
				$domain = preg_replace($mediaServers['search_regex_pattern'], $mediaServers['replacement'], $domain);

			if (isset ($mediaServers['appPrefix']))
				$appPrefix = $mediaServers['appPrefix'];
			
			if (isset ($mediaServers['dc-'.$this->getDc()]))
		    {
		    	$mediaServer = $mediaServers['dc-'.$this->getDc()];
		    
		    	if(isset($mediaServer[$portField]))
		     		$port = $mediaServer[$portField];
		    
		    	if(isset($mediaServer['domain']))
		     		$domain = $mediaServer['domain'];
		     	
		     	if (isset ($mediaServer['appPrefix']))
					$appPrefix = $mediaServer['appPrefix'];
		    }
				
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer[$portField]))
					$port = $mediaServer[$portField];
				
				if(isset($mediaServer['domain']))
					$domain = $mediaServer['domain'];
				
				if (isset ($mediaServer['appPrefix']))
					$appPrefix = $mediaServer['appPrefix'];
			}
		}
		
		$hostname = $this->getHostname();
		if(!$this->isExternalMediaServer)
			$hostname = preg_replace('/\..*$/', '', $hostname);
		
		$url = "$protocol://$domain:$port/$appPrefix";
		$url = str_replace("{hostName}", $hostname, $url);
		return $url;
		
	}
	
	/**
	 * @param string $service
	 * @return KalturaMediaServerClient
	 */
	public function getWebService($service)
	{	
		if(!isset(self::$webServices[$service]))
			return null;
			
		$serviceClass = self::$webServices[$service];
			
		$domain = $this->getHostname();
		$port = MediaServer::DEFAULT_WEB_SERVICES_PORT;
		$protocol = 'http';
		
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			if(isset($mediaServers['service-port']))
				$port = $mediaServers['service-port'];
				
			if(isset($mediaServers['protocol']))
				$protocol = $mediaServers['protocol'];
				
			if(isset($mediaServers['internal_domain']))
				$domain = $mediaServers['internal_domain'];
			elseif(isset($mediaServers['internal_search_regex_pattern']) && isset($mediaServers['internal_replacement']))
				$domain = preg_replace($mediaServers['internal_search_regex_pattern'], $mediaServers['internal_replacement'], $domain);
				
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer['service-port']))
					$port = $mediaServer['service-port'];
				
				if(isset($mediaServer['protocol']))
					$protocol = $mediaServer['protocol'];
					
				if(isset($mediaServer['internal_domain']))
					$domain = $mediaServer['internal_domain'];
			}
		}
		
		$url = "$protocol://$domain:$port/$service?wsdl";
		KalturaLog::debug("Service URL: $url");
		return new $serviceClass($url);
	}
	
	public function setIsExternalMediaServer($v)
	{
		$this->isExternalMediaServer = $v;
	}
	
} // MediaServer
