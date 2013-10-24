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
	
	public function getManifestUrl()
	{
		$domain = $this->getHostname();
		$port = MediaServer::DEFAULT_MANIFEST_PORT;
		
		if(kConf::hasMap('media_servers'))
		{
			$mediaServers = kConf::getMap('media_servers');
			if(isset($mediaServers['port']))
				$port = $mediaServers['port'];
				
			if(isset($mediaServers['search_regex_pattern']) && isset($mediaServers['replacement']))
				$domain = preg_replace($mediaServers['search_regex_pattern'], $mediaServers['replacement'], $domain);
				
			if(isset($mediaServers[$this->getHostname()]))
			{
				$mediaServer = $mediaServers[$this->getHostname()];
				
				if(isset($mediaServer['port']))
					$port = $mediaServer['port'];
					
				if(isset($mediaServer['domain']))
					$domain = $mediaServer['domain'];
			}
		}
		
		return "http://$domain:$port";
	}
	
} // MediaServer
