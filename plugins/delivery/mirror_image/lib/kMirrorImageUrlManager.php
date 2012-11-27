<?php
/**
 * @package plugins.mirrorImage
 * @subpackage storage
 */
class kMirrorImageUrlManager extends kUrlManager
{
    const DEFAULT_ACCESS_WINDOW_SECONDS = 900; // 900 seconds = 15 minutes
    
	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		$secret = null;
		switch ($this->protocol)
		{
		case PlaybackProtocol::HTTP:			
			if (@$this->params['http_auth_salt'])
			{
			   $storageProfile = StorageProfilePeer::retrieveByPK($this->storageProfileId);
				if ($storageProfile)
				{
					// get parameters
					$window = $this->params['http_auth_seconds'];
					$secret = $this->params['http_auth_salt'];
					$useDummyHost = false;	
					$httpBaseUrl = rtrim($storageProfile->getDeliveryHttpBaseUrl(), '/');
				}
			}
			break;

		case PlaybackProtocol::RTMP:
			if (@$this->params['rtmp_auth_salt'])
			{
				$window = $this->params['rtmp_auth_seconds'];
				$secret = $this->params['rtmp_auth_salt'];
				$useDummyHost = true;      	
				$httpBaseUrl = '';
			}
			break;
		}

		if ($secret)
		{
			if (is_null($window) || !is_int($window))
			{
				$window = self::DEFAULT_ACCESS_WINDOW_SECONDS;
			}		
			return new kMirrorImageUrlTokenizer($window, $secret, $useDummyHost, $httpBaseUrl);
		}
		
		return null;
	}
}
