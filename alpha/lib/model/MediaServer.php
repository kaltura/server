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
	const DEFAULT_APPLICATION = 'kLive';
	const DEFAULT_TRANSCODER = 'default';
	const DEFAULT_GPUID = -1;
	
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
	
	public function getRtmpUrl()
	{
		$domain = $this->getHostname();
		$app = MediaServer::DEFAULT_APPLICATION;
		
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			if(isset($mediaServers['application']))
				$app = $mediaServers['application'];
				
			if(isset($mediaServers['search_regex_pattern']) && isset($mediaServers['replacement']))
				$domain = preg_replace($mediaServers['search_regex_pattern'], $mediaServers['replacement'], $domain);
				
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer['application']))
					$app = $mediaServer['application'];
					
				if(isset($mediaServer['domain']))
					$domain = $mediaServer['domain'];
			}
		}
		
		return "rtmp://$domain/$app/p";
	}
	
	public function getManifestUrl($protocol = 'http')
	{
		$domain = $this->getHostname();
		$port = MediaServer::DEFAULT_MANIFEST_PORT;
		$app = MediaServer::DEFAULT_APPLICATION;
		$portField = 'port';
		if($protocol != 'http')
			$portField .= "-$protocol";
		
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			if(isset($mediaServers[$portField]))
				$port = $mediaServers[$portField];
				
			if(isset($mediaServers['application']))
				$app = $mediaServers['application'];
				
			if(isset($mediaServers['domain']))
				$domain = $mediaServers['domain'];
			elseif(isset($mediaServers['search_regex_pattern']) && isset($mediaServers['replacement']))
				$domain = preg_replace($mediaServers['search_regex_pattern'], $mediaServers['replacement'], $domain);
				
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer[$portField]))
					$port = $mediaServer[$portField];
				
				if(isset($mediaServer['application']))
					$app = $mediaServer['application'];
					
				if(isset($mediaServer['domain']))
					$domain = $mediaServer['domain'];
			}
		}
		
		return "$protocol://$domain:$port/$app/p/";
	}
	
} // MediaServer
